
function traverse(tree, list_id, hide_links, folder_options) {
    if (typeof hide_links === undefined) hide_links =  false;
    if (typeof folder_options === undefined) optios_above_folders =  false;

    var html = [];

    if (typeof list_id === 'undefined') {
        html.push('<ul>');
    }
    else {
        html.push('<ul id="'+ list_id +'">');
    }

    var mailboxes_to = [];
    for (var attr in tree) {
        var localized = attr.replace(/^inbox$/i,  'Entrada')
                            .replace(/^drafts$/i, 'Rascunhos')
                            .replace(/^trash$/i,  'Lixeira')
                            .replace(/^sent$/i,   'Enviados')
                            .replace(/^junk$/i,   'Spam');

        mailboxes_to.push({
            'original': tree[attr][0],
            'status': tree[attr][1],
            'nextnode': tree[attr][2],
            'mailbox_default_name': attr, // comes uppon utf-7 encode
            'mailbox_name': localized,
        });
    }

    //sorting mailboxes by mailbox_name letting inbox on top.
    order_mailboxes(mailboxes_to);

    for (var mailbox in mailboxes_to ){
        var original = mailboxes_to[mailbox].original;
        //var status   = mailboxes_to[mailbox].status;
        var nextnode = mailboxes_to[mailbox].nextnode;
        var mailbox_default_name = mailboxes_to[mailbox].mailbox_default_name; // comes uppon utf-7 encode
        var mailbox_name = mailboxes_to[mailbox].mailbox_name;

        var link_to_mailbox = (!hide_links)? '?box='+ encodeURI(mailbox_default_name) +'&boxfull='+ encodeURI(original): '#';
        original = original.replace(/\.Inbox$/i,''); //INFO EncodeURI decodes utf7 to uriPATH

        if(folder_options){
            var html_opt = [
                '<img id="folder_del" title="Deletar" src="assets/img/btn-delete.png" width="auto" height="20" />',
                '<img id="folder_edit" title="Renomear" src="assets/img/btn-edit.png" width="auto" height="25"/>',
                '<div class="clear"></div>'
            ].join("\n");
        }

        if (nextnode == null) {
            html.push([
                '<li>',
                '   <a href="'+ link_to_mailbox +'" data-box="'+ original +'" data-box_name="'+ mailbox_name +'">',
                        mailbox_name +' '+ ((!folder_options)? '': ''), //status are hacked... retrive this info by parsing list_emails.php return JSON
                '   </a>'+ ((folder_options)? html_opt: ''),
                '</li>',
            ].join("\n"));

        } else {
            html.push([
                '<li>',
                '   <span>',
                '       <a href="'+ link_to_mailbox +'" data-box="'+ original +'" data-box_name="'+ mailbox_name +'">',
                            mailbox_name +' '+ ((!folder_options)? '': ''), //status are hacked... retrive this info by parsing list_emails.php return JSON
                '       </a>' + ((folder_options)? html_opt: ''),
                '   </span>',
                traverse(nextnode, undefined, hide_links, folder_options),
            '</li>'].join("\n"));

        }
    }
    html.push('</ul>');

    return html.join('\n');
}

//Repeat [str] N times
function strRepeat(str, n){
    var a = [];
    while(a.length < n){
        a.push(str);
    }
    return a.join('');
}

// !?
function order_mailboxes(mailboxes) {
//    console.log(mailboxes);
    for (var name in mailboxes) {
        if (mailboxes[name].mailbox_name.toLowerCase().match(/entrada/gi))  mailboxes[name].mailbox_name = '1'+mailboxes[name].mailbox_name;
    }

    mailboxes.sort(function(a, b){
        return a.mailbox_name.toLowerCase().localeCompare(b.mailbox_name.toLowerCase());
    });

    for (var name in mailboxes) {
        if (mailboxes[name].mailbox_name.toLowerCase().match(/entrada/gi))  mailboxes[name].mailbox_name = mailboxes[name].mailbox_name.replace(/^\d/,'');
    }

    return mailboxes;
}

function IsJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}