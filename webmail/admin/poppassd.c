/*
 * poppassd.c
 *
 * A Eudora and NUPOP change password server.
 *
 * John Norstad
 * Academic Computing and Network Services
 * Northwestern University
 * j-norstad@nwu.edu
 *
 * Based on earlier versions by Roy Smith <roy@nyu.edu> and Daniel
 * L. Leavitt <dll.mitre.org>.
 *
 * Doesn't actually change any passwords itself.  It simply listens for
 * incoming requests, gathers the required information (user name, old
 * password, new password) and executes /bin/passwd, talking to it over
 * a pseudo-terminal pair.  The advantage of this is that we don't need
 * to have any knowledge of either the password file format (which may
 * include dbx files that need to be rebuilt) or of any file locking
 * protocol /bin/passwd and cohorts may use (and which isn't documented).
 *
 * The current version has been tested at NU under SunOS release 4.1.2
 * and 4.1.3, and under HP-UX 8.02 and 9.01. We have tested the server
 * with both Eudora 1.3.1 and NUPOP 2.0.
 *
 * Other sites report that this version also works under AIX and NIS,
 * and with PC Eudora.
 *
 * Note that unencrypted passwords are transmitted over the network.  If
 * this bothers you, think hard about whether you want to implement the
 * password changing feature.  On the other hand, it's no worse than what
 * happens when you run /bin/passwd while connected via telnet or rlogin.
 * Well, maybe it is, since the use of a dedicated port makes it slightly
 * easier for a network snooper to snarf passwords off the wire.
 *
 * NOTE: In addition to the security issue outlined in the above paragraph,
 * you should be aware that this program is going to be run as root by
 * ordinary users and it mucks around with the password file.  This should
 * set alarms off in your head.  I think I've devised a pretty foolproof
 * way to ensure that security is maintained, but I'm no security expert and
 * you would be a fool to install this without first reading the code and
 * ensuring yourself that what I consider safe is good enough for you.  If
 * something goes wrong, it's your fault, not mine.
 *
 * The front-end code (which talks to the client) is directly
 * descended from Leavitt's original version.  The back-end pseudo-tty stuff
 * (which talks to /bin/password) is directly descended from Smith's
 * version, with changes for SunOS and HP-UX by Norstad (with help from
 * sample code in "Advanced Programming in the UNIX Environment"
 * by W. Richard Stevens). The code to report /bin/passwd error messages
 * back to the client in the final 500 response, and a new version of the
 * code to find the next free pty, is by Norstad.
 *
 * Should be owned by root, and executable only by root.  It can be started
 * with an entry in /etc/inetd.conf such as the following:
 *
 * poppassd stream tcp nowait root /usr/local/bin/poppassd poppassd
 *
 * and in /etc/services:
 *
 * poppassd	106/tcp
 *
 * Logs to the local2 facility. Should have an entry in /etc/syslog.conf
 * like the following:
 *
 * local2.err	/var/adm/poppassd-log
 */

/* Modification history.
 *
 * 06/09/93. Version 1.0.
 *
 * 06/29/93. Version 1.1.
 * Include program name 'poppassd' and version number in initial
 *    hello message.
 * Case insensitive command keywords (user, pass, newpass, quit).
 *    Fixes problem reported by Raoul Schaffner with PC Eudora.
 * Read 'quit' command from client instead of just terminating after
 *    password change.
 * Add new code for NIS support (contributed by Max Caines).
 *
 * 08/31/93. Version 1.2.
 * Generalized the expected string matching to solve several problems
 *    with NIS and AIX. The new "*" character in pattern strings
 *    matches any sequence of 0 or more characters.
 * Fix an error in the "getemess" function which could cause the
 *    program to hang if more than one string was defined in the
 *    P2 array.

 * 6/4/20000 version 1.6a
 *  Updated Code to use PIPES, and also added save passwd output to file.
 */

/* Steve Dorner's description of the simple protocol:
 *
 * The server's responses should be like an FTP server's responses;
 * 1xx for in progress, 2xx for success, 3xx for more information
 * needed, 4xx for temporary failure, and 5xx for permanent failure.
 * Putting it all together, here's a sample conversation:
 *
 *   S: 200 machine_name popassd v1.4 hello, who are you?\r\n
 *   E: user yourloginname\r\n
 *   S: 200 your password please.
 *   E: pass yourcurrentpassword\r\n
 *   S: 200 your new password please.\r\n
 *   E: newpass yournewpassword\r\n
 *   200 Password changed, thank-you.\r\n
 *   E: quit\r\n
 *   S: 200 Bye-bye\r\n
 *   S: <closes connection>
 *   E: <closes connection>
 */

#define HAS_SHADOW
#define FRED
/* #define AIX */
#define RED_HAT_LINUX_6_0
/* #define BSD_2_1 */



#define VERSION "1.6a"

#define SUCCESS 1
#define FAILURE 0
#define BUFSIZE 512

#include <sys/types.h>
#include <sys/stat.h>
#include <sys/wait.h>
#include <unistd.h>
#include <fcntl.h>
#include <syslog.h>
#include <stdlib.h>
#include <stdio.h>
#include <ctype.h>
#include <strings.h>
#include <errno.h>
#include <stdarg.h>
#include <pwd.h>
#include <string.h>
#include <termios.h>
#include <dirent.h>
#ifndef BSD_2_1
#ifndef AIX
#	include <getopt.h>
#endif
#endif

#ifdef HAS_SHADOW
#	include <shadow.h>
/*#	include <shadow/pwauth.h> */
#	ifndef PW_PPP
#		define PW_PPP PW_LOGIN
#	endif

#	ifndef RED_HAT_LINUX_6_0
		char *pw_encrypt (char *, char *);	/* To permit long shadow passwords */
#		define crypt pw_encrypt		/* for short passwords as well.    */
#	endif

#endif

/*
 * Prototypes
 */

int main (int argc, char *argv[]);
int process(int *ToChild, int *FromChild);
int dochild (char *slavedev, char *user);
int findpty (char **slave);
void writestring (int fd, char *s);
char *talktochild ( int master_read, int master_write, char *user, char *oldpass,
					char *newpass, char *emess);
int match (char *str, char *pat);
char *expect (int master, char **expected, char *buf);
int getemess (int master, char **expected, char *buf);
void WriteToClient (char *fmt, ...);
void ReadFromClient (char *line);
int chkPass (char *user, char *pass, struct passwd *pw);

/* Prompt strings expected from the "passwd" command. If you want
 * to port this program to yet another flavor of UNIX, you may need to add
 * more prompt strings here.
 *
 * Each prompt is defined as an array of pointers to alternate
 * strings, terminated by an empty string. In the strings, '*'
 * matches any sequence of 0 or more characters. Pattern matching
 * is forced to lower case so enter only lower case letters.
 */

static char *P1[] =
   {
	 "password: ",
     "changing password for *\nenter old password: ",
#if 0
     "changing nis password for * on *.\nold password: ",
#endif
	 "changing local password for *",				/* BSD v2.1 */
	 ""
   };

static char *P2[] =
   {
	 "Error changing password for *\nchanging password for *",
						/* IBM's AIX 4.3.3. */
	 "new password:",
	 "new unix password:",
	 "changing password for *\nenter new password:",/* Cobalt Linux 4.0 (mips) */
	 "changing password for *\nnew unix password: ",/* Red Hat Linux 6.0 */
	 "changing password for *\nnew unix password:",	/* RedHat Linux 5.2 */
	 "changing password for *\n*'s new password:",	/* IBM's AIX 4.2.1. */
     "enter new password:",							/* non-shadow passwords */
     "changing password for *\nnew password:",		/* shadow passwords     */
	 "New password (8 significant characters):",	/* BSD v2.1 */
	 ""
   };

/* New password (8 significant characters):
Please don't use an all-lower case password.
Unusual capitalization, control charactersor digits are suggested.
New password (8 significant characters):*/

static char *P3[] =
   {
	 "new password (again):",
	 "retype new unix password:",		/* RedHat Linux 5.2 */
	 "re-enter *'s new password:",		/* IBM's AIX 4.2.1. */
     "re-type new password:",			/* non-shadow passwords	*/
     "re-enter new password:",			/* shadow passwords	*/
	 "retype new password:",			/* BSD v2.1 */
     ""
   };

static char *P4[] =
   {
     "password changed",
     "nis entry changed on *",
	 "passwd: all authentication tokens updated successfully",	/* RedHat Linux 5.2 + 6.0 */
	 "passwd:*updating passwd database*passwd:*done*",			/* BSD v2.1 */
     ""
   };

int verbose = 0;
int debug_mode = 0;
FILE *logfile = NULL;

struct passwd *pw;
char user[BUFSIZE];

int main (int argc, char *argv[])
{
	int res;
	static int ToChild[2], FromChild[2];
	int i;

/* Check for Args */
	for (i=1;i<argc;i++) {
		switch (*argv[i]) {
			case 'v':
				verbose = 1;
				break;
			case 'd':
				fprintf (stderr, "Debug mode on\n");
				debug_mode = 1;
			/*	verbose = 1; */
				break;
			case 'l':
				fprintf (stderr, "Lower Debug mode on\n");
				debug_mode = -1;
			/*	verbose = 1; */
				break;
			case 'f':
				logfile = fopen("temp.log","wb");
				break;

			default:
				fprintf (stderr, "invalid option {%s}\n", argv[i]);
				exit (1);
		}
	}


/* Setup input/output to child using pipes. */
/* **************************************** */
	if (pipe(ToChild) != 0 || pipe(FromChild) != 0) {
		WriteToClient("500 Can't create pipes. {%s}",strerror(errno));
		return 1;
	}
	else if (debug_mode > 0) {
		printf("Pipe 1: {%d/%d}\n",ToChild[0],ToChild[1]);
		printf("Pipe 2: {%d/%d}\n",FromChild[0],FromChild[1]);
	}


	res = process(ToChild,FromChild);

/* Close Pipes */
	close(ToChild[0]);
	close(ToChild[1]);
	close(FromChild[0]);
	close(FromChild[1]);

	if (logfile)
		fclose(logfile);
	return res;
}

int process(int *ToChild, int *FromChild)
{
/* PIPE stuff*/
	char *error_str;
	char line[BUFSIZE];
	char oldpass[BUFSIZE];
	char newpass[BUFSIZE];
	char emess[BUFSIZE];
	char *slavedev = NULL;
	int c;
/*	int master; */
	pid_t pid, wpid;
	int wstat;

#ifdef HAS_SHADOW
    struct spwd *spwd;
    struct spwd *getspnam();
#endif

	*user    =
	*oldpass =
	*newpass = 0;

	openlog ("poppassd", LOG_PID, LOG_LOCAL2);

	gethostname(line, sizeof (line));
	WriteToClient ("200 %s poppassd v%s hello, who are you?", line, VERSION);

	ReadFromClient (line);
	sscanf (line, "user %s", user) ;
	if (strlen (user) == 0) {
		WriteToClient ("500 Username required.");
		return(1);
	}

	WriteToClient ("200 your password please.");
	ReadFromClient (line);
	sscanf (line, "pass %s", oldpass) ;

	if (strlen (oldpass) == 0) {
		WriteToClient ("500 Password required.");
		return(1);
	}

	if ((pw = getpwnam (user)) == NULL) {
		WriteToClient ("500 Invalid user or password");
		return(1);
	}

#ifdef HAS_SHADOW
    if ((spwd = getspnam(user)) == NULL)
	  pw->pw_passwd = "";
    else
	  pw->pw_passwd = spwd->sp_pwdp;
#endif

	if (chkPass (user, oldpass, pw) == FAILURE) {
		sleep(3);
		WriteToClient ("500 Invalid user or password");
		return(1);
	}


	WriteToClient ("200 your new password please.");
	ReadFromClient (line);
	sscanf (line, "newpass %s", newpass);

	/* new pass required */
	if (strlen (newpass) == 0) {
		WriteToClient ("500 New password required.");
		return(1);
	}

    /* fork child process to talk to password program */
	if ((pid = fork()) < 0)     /* Error, can't fork */
	{
		syslog (LOG_ERR, "can't fork for passwd: %m");
		WriteToClient ("500 Server error (can't fork passwd), get help!");
		return (1);
	}

	if (pid)   /* Parent */
	{
		error_str = talktochild (FromChild[0], ToChild[1], user, oldpass, newpass, emess);
		if ( error_str ) {

			if (debug_mode > 0) {
				if (*emess == '\0')
					printf("Password Failed {%s}\n",error_str);
				else
					printf("Password Failed {%s}\n",emess);
			}

			syslog (LOG_ERR, "failed attempt by %s", user);
			if (*emess == '\0')
				WriteToClient ("500 '%s'.",error_str );
			else
				WriteToClient ("500 '%s'", emess);
			return(1);
		}

		if (debug_mode  > 0)
			printf("Finished Talking to Child\n");
#ifndef BSD_2_1
		if ((wpid = waitpid (pid, &wstat, 0)) < 0) {
			syslog (LOG_ERR, "wait for /bin/passwd child failed: %m");
			WriteToClient ("500 Server error (wait failed), get help!");
			return (1);
		}

		if (debug_mode > 0)
			printf("Checking Child Process ID\n");

		if (pid != wpid) {
			syslog (LOG_ERR, "wrong child (/bin/passwd waited for!");
			WriteToClient ("500 Server error (wrong child), get help!");
			return (1);
		}

		if (WIFEXITED (wstat) == 0) {
			syslog (LOG_ERR, "child (/bin/passwd) killed?");
			WriteToClient ("500 Server error (funny wstat), get help!");
			return (1);
		}

		if (WEXITSTATUS (wstat) != 0) {
			syslog (LOG_ERR, "child (/bin/passwd) exited abnormally");
			WriteToClient ("500 Server error (abnormal exit), get help!");
			return (1);
		}
#endif
		if (debug_mode > 0)
			printf("Child Process has finished\n");

		syslog (LOG_ERR, "password changed for %s", user);
		WriteToClient ("200 Password changed, thank-you.");

		ReadFromClient (line);
		if (strncmp(line, "quit", 4) != 0) {
			WriteToClient("500 Quit required.");
			return (1);
		}

		WriteToClient("200 Bye.");
		return (0);
	}
	else      /* Child */
	{
	/* Setup Child input/output */
		dup2(ToChild[0],STDIN_FILENO);
	/*	dup2(FromChild[1],STDOUT_FILENO); */
		dup2(FromChild[1],STDERR_FILENO);

		dochild (slavedev, user);
	}
}

/*
 * dochild
 *
 * Do child stuff - set up slave pty and execl /bin/passwd.
 *
 * Code adapted from "Advanced Programming in the UNIX Environment"
 * by W. Richard Stevens.
 *
 */

int dochild (char *slavedev, char *user)
{
   int slave;
   struct termios stermios;

   /* Start new session - gets rid of controlling terminal. */

   if (setsid() < 0) {
      syslog(LOG_ERR, "setsid failed: %m");
      return(0);
   }

   /* Do some simple changes to ensure that the daemon does not mess */
   /* things up. */

   if (!debug_mode)
		chdir ("/");
   umask (0);

/*
 * Shadow password suite looks the user in the login database. Since
 * poppassd does not 'login', it will fail. So, cheat. Keep root status
 * and pass the user on the command line.
 */

   if (debug_mode > 0)
		execl("./dummy",				"dummy", user, (char*)0);


#ifdef HAS_SHADOW
   execl("/bin/passwd",     "passwd", user, (char*)0);
   execl("/usr/bin/passwd", "passwd", user, (char*)0);
#else

#ifdef FRED
   execl("/bin/passwd",     "passwd", user, (char*)0);
   execl("/usr/bin/passwd", "passwd", user, (char*)0);
#else

/*
 * Without the shadow password suite, the standard password program
 * looks at the uid for the user. Become the user and don't pass it
 * on the command line.
 */
   setregid (pw->pw_gid, pw->pw_gid);
   setreuid (pw->pw_uid, pw->pw_uid);

   execl("/bin/passwd",     "passwd",       (char*)0);
   execl("/usr/bin/passwd", "passwd",       (char*)0);
#endif
#endif

   syslog(LOG_ERR, "can't exec /bin/passwd: %m");
   return(0);
}


/*
 * findpty()
 *
 * Finds the first available pseudo-terminal master/slave pair.  The master
 * side is opened and a fd returned as the function value.  A pointer to the
 * name of the slave side (i.e. "/dev/ttyp0") is returned in the argument,
 * which should be a char**.  The name itself is stored in a static buffer.
 *
 * A negative value is returned on any sort of error.
 *
 * Modified by Norstad to remove assumptions about number of pty's allocated
 * on this UNIX box.
 */

int findpty (char **slave)
{
   int master;
   static char line[] = "/dev/ptyXX";
   DIR *dirp;
   struct dirent *dp;

   dirp = opendir("/dev");
   while ((dp = readdir(dirp)) != NULL) {
      if (strncmp(dp->d_name, "pty", 3) == 0 && strlen(dp->d_name) == 5) {
         line[8] = dp->d_name[3];
         line[9] = dp->d_name[4];
         if ((master = open(line, O_RDWR)) >= 0) {
            line[5] = 't';
            *slave = line;
            closedir(dirp);
            return (master);
         }
      }
   }
   closedir(dirp);
   return (-1);
}

/*
 * writestring()
 *
 * Write a string in a single write() system call.
 */
void writestring (int fd, char *s)
{
     int l;

     l = strlen (s);
     write (fd, s, l);
     if (verbose)
         syslog(LOG_DEBUG, "write: %s", s);
}

/*
 * talktochild()
 *
 * Handles the conversation between the parent and child (password program)
 * processes.
 *
 * Returns SUCCESS is the conversation is completed without any problems,
 * FAILURE if any errors are encountered (in which case, it can be assumed
 * that the password wasn't changed).
 */
char *talktochild ( int master_read, int master_write, char *user, char *oldpass, char *newpass,
					char *emess)
{
	char *error_str;
	static char buf[BUFSIZE];
	char pswd[BUFSIZE+1];
	int m, n;

	*emess = 0;

#ifndef HAS_SHADOW
#ifndef FRED
	 if (debug_mode)
		printf("Stage 1.\n");
	 if (logfile) {
		 fprintf(logfile,"Stage 1.\n");
		 fflush(logfile);
	 }

	 error_str = expect(master_read, P1, buf);
     if (error_str) return error_str;

     sprintf(pswd, "%s\n", oldpass);
     writestring(master_write, pswd);
#endif
#endif

 	 if (debug_mode)
		printf("Stage 2.\n");
	 if (logfile) {
		 fprintf(logfile,"Stage 2.\n");
		 fflush(logfile);
	 }

	 error_str = expect(master_read, P2, buf);
     if (error_str) return error_str;


	 if (debug_mode > 0)
		printf("Sending New Password...");

	 sprintf(pswd, "%s\n", newpass);
     writestring(master_write, pswd);

	 if (debug_mode > 0)
		printf("done\n");

	 if (debug_mode)
		printf("Stage 3.\n");
	 if (logfile) {
		 fprintf(logfile,"Stage 3.\n");
		 fflush(logfile);
	 }

	 error_str = expect(master_read, P3, buf);
     if (error_str) {
		 int OK = 0;
#ifdef BSD_2_1
		char **s = P2, *p = error_str;

		if (debug_mode)
			printf("Checking Stage 2 again.\n");
		if (logfile) {
			fprintf(logfile,"Checking Stage 2 again.\n");
			fflush(logfile);
		}

		for (s = P2; **s != 0; s++) {

			if (debug_mode > 0)
				printf(" '%s'=='%s'??\n",p,*s);
			if (logfile) {
				fprintf(logfile," '%s'=='%s'??\n",p,*s);
				fflush(logfile);
			}

			switch (match(p, *s)) {
				case 2:
					if (verbose)
						syslog (LOG_DEBUG, "expect: succes\n");
					OK = 1;
			}
		}

		if (!OK) {
			if (debug_mode)
				printf("Stage 3 FAILED. {%s}\n",buf);
		   /* getemess(master_read, P2, buf); */
			if (logfile) {
				 fprintf(logfile,"Stage 3 FAILED. {%s}\n",buf);
				 fflush(logfile);
			}

			strcpy(emess, buf);
			return error_str;
		}
		else {
			if (debug_mode > 0)
				printf("Sending New Password...");

			writestring(master_write, pswd);

			if (debug_mode > 0)
				printf("done\n");

			error_str = expect(master_read, P3, buf);
			if (error_str)
				OK = 1;
		}
#endif
		if (!OK) {
			if (debug_mode)
				printf("Stage 3 FAILED. {%s}\n",buf);
		   /* getemess(master_read, P2, buf); */
			if (logfile) {
				 fprintf(logfile,"Stage 3 FAILED. {%s}\n",buf);
				 fflush(logfile);
			}

			strcpy(emess, buf);
			return error_str;
		}

     }

	 if (debug_mode > 0)
		printf("Sending New Password...");

     writestring(master_write, pswd);

	 if (debug_mode > 0)
		printf("done\n");

#ifndef HAS_SHADOW  /* shadow prints no success message :( */
#ifndef AIX			/* AIX prints no success message :( */
	 error_str = expect(master_read, P4, buf);
     if (error_str) return error_str;
#endif
#endif

#ifdef RED_HAT_LINUX_6_0
	 error_str = expect(master_read, P4, buf);
     if (error_str) return error_str;
#endif

	 if (logfile) {
		 fprintf(logfile,"Finished.\n");
		 fflush(logfile);
	 }
     return NULL;
}

/*
 * match ()
 *
 * Matches a string against a pattern. Wild-card characters '*' in
 * the pattern match any sequence of 0 or more characters in the string.
 * The match is case-insensitive.
 *
 * Entry: str = string.
 *        pat = pattern.
 *
 * Exit:  function result =
 *		0 if no match.
 *		1 if the string matches some initial segment of
 *		  the pattern.
 *		2 if the string matches the full pattern.
 */

#define	NO_MATCH		0
#define	PART_MATCH		1
#define	MATCH			2

int match(char *text, char *p)
{
    register int	last;
    register int	matched;
    register int	reverse;

    for ( ; *p; text++, p++) {
		if (*text == '\0' && *p != '*')
			return PART_MATCH;
		switch (*p) {
			case '\n':
			case '\r':
				text--;
				continue;
			default:
				if (tolower(*text) != tolower(*p))
					return NO_MATCH;
				continue;
			case '?':
				/* Match anything. */
				continue;
			case '*':
				while (*++p == '*')
					/* Consecutive stars act just like one. */
					continue;
				if (*p == '\0')
					/* Trailing star matches everything. */
					return MATCH;
				while (*text)
					if ((matched = match(text++, p)) != NO_MATCH)
						return matched;
				return PART_MATCH;
		}
    }
	if (*text == '\0')
		return MATCH;
	return PART_MATCH;
}


/*
 * expect ()
 *
 * Reads 'passwd' command output and compares it to expected output.
 *
 * Entry: master = fid of master pty.
 *	  expected = pointer to array of pointers to alternate expected
 *            strings, terminated by an empty string.
 *        buf = pointer to buffer.
 *
 * Exit:  function result = SUCCESS if output matched, FAILURE if not.
 *        buf = the text read from the slave.
 *
 * Text is read from the slave and accumulated in buf. As long as
 * the text accumulated so far is an initial segment of at least
 * one of the expected strings, the function continues the read.
 * As soon as one of full expected strings has been read, the
 * function returns SUCCESS. As soon as the text accumulated so far
 * is not an initial segment of or exact match for at least one of
 * the expected strings, the function returns FAILURE.
 */

char *expect (int master_read, char **expected, char *buf)
{
	char *error_str;

	int n, m, count = 0;
	char **s;
	char *p;
	int initialSegment = 0;

	errno = 0;
	buf[0] = 0;
	while (1) {
		n = strlen (buf);
     	if (n >= BUFSIZE-1) {
			syslog(LOG_ERR, "buffer overflow on read from child");
			return buf;
		}

		if (debug_mode > 0)
			printf("Reading from 'passwd'\n");
		if (logfile) {
			 fprintf(logfile,"Reading from 'passwd'\n");
			 fflush(logfile);
		}

		m = read (master_read, &buf[n], BUFSIZ-1-n);
		if (debug_mode > 0)
			printf("Read %d bytes\n",m);
		if (logfile) {
			 fprintf(logfile,"Read %d bytes\n",m);
			 fflush(logfile);
		}

		if (m < 0) {
			if (debug_mode > 0)
				printf("Error Reading %s\n",strerror(errno));
			if (logfile) {
				 fprintf(logfile,"Error Reading %s\n",strerror(errno));
				 fflush(logfile);
			}
			syslog(LOG_ERR, "read error from child: %m");
			return buf;
		}
		buf[n+m] = '\0';

	/* remove '\r' and '\n' and ' ' (spaces)*/
		p = &buf[n] + strlen(&buf[n]) - 1;
		while (*p == '\r' || *p == '\n' || (*p == ' ' && !initialSegment)) p--;
		*++p = '\0';

		if (verbose)
			syslog (LOG_DEBUG, "read: %s\n", &buf[n]);
		if (debug_mode > 0)
			printf("read: '%s'\n", buf);
		if (logfile) {
			 fprintf(logfile,"read: '%s'\n", buf);
			 fflush(logfile);
		}

	/* Ignore leading whitespace. It gets in the way. */
		p = buf;
		while (isspace (*p))
			++p;

		if (*p == '\0') {
			count++;
			if (count == 100)
				return "Failed to read any data from passwd";
			continue;
		}

		initialSegment = 0;
		for (s = expected; **s != 0; s++) {

			if (debug_mode > 0)
				printf(" '%s'=='%s'??\n",p,*s);
			if (logfile) {
				 fprintf(logfile," '%s'=='%s'??\n",p,*s);
				 fflush(logfile);
			}

			switch (match(p, *s)) {
				case 2:
					if (verbose)
						syslog (LOG_DEBUG, "expect: succes\n");
					if (debug_mode > 0)
						printf("    - Located Match (%s)\n",p);
					return NULL;
				case 1:
					initialSegment = 1;
				default:
					break;
			}
		}

		if (!initialSegment) {
			if (verbose)
				syslog (LOG_DEBUG, "expect: failure\n");
			if (debug_mode > 0)
				printf("    - Failed to Match\n");
			return buf;
		}
	}
}

/*
 * getemess()
 *
 * This function accumulates a 'passwd' command error message issued
 * after the first copy of the password has been sent.
 *
 * Entry: master = fid of master pty.
 *	  expected = pointer to array of pointers to alternate expected
 *            strings for first password prompt, terminated by an
 *            empty string.
 *        buf = pointer to buffer containing text read so far.
 *
 * Exit:  buf = the error message read from the slave.
 *
 * Text is read from the slave and accumulated in buf until the text
 * at the end of the buffer is an exact match for one of the expected
 * prompt strings. The expected prompt string is removed from the buffer,
 * returning just the error message text. Newlines in the error message
 * text are replaced by spaces.
 */
int getemess (int master_read, char **expected, char *buf)
{
   int n, m;
   char **s;
   char *p, *q;

   n = strlen(buf);
   while (1) {
      for (s = expected; **s != 0; s++) {
         for (p = buf; *p; p++) {
            if (match(p, *s) == 2) {
               *p = 0;
               for (q = buf; *q; q++) if (*q == '\n') *q = ' ';
               return;
            }
         }
      }
      if (n >= BUFSIZE-1) {
	 syslog(LOG_ERR, "buffer overflow on read from child");
	 return;
      }
      m = read(master_read, buf+n, BUFSIZE+1-n);
      if (m < 0) {
	 syslog(LOG_ERR, "read error from child: %m");
	 return;
      }
      n += m;
      buf[n] = 0;

      if (verbose)
		syslog (LOG_DEBUG, "read: %s\n", buf);
   }
}

void WriteToClient (char *fmt, ...)
{
	va_list ap;

	va_start (ap, fmt);
	vfprintf (stdout, fmt, ap);
	fputs ("\r\n", stdout );
	fflush (stdout);
	va_end (ap);
}

void ReadFromClient (char *line)
{
	char *sp;
	int i;

	strcpy (line, "");
	fgets (line, BUFSIZE, stdin);
	if ((sp = strchr(line, '\n')) != NULL) *sp = '\0';
	if ((sp = strchr(line, '\r')) != NULL) *sp = '\0';

	/* convert initial keyword on line to lower case. */

	for (sp = line; isalpha(*sp); sp++) *sp = tolower(*sp);
}

#ifndef crypt
	char *crypt(const char *key, const char *salt);
#endif

int chkPass (char *user, char *pass, struct passwd *pw)
{
     /*  Compare the supplied password with the password file entry */
	if (strcmp (crypt (pass, pw->pw_passwd), pw->pw_passwd) != 0)
		return (FAILURE);
	else
		return (SUCCESS);
}