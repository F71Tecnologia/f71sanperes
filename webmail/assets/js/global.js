var GLOBALS = {
	cron_save_draft: null,
	cron_keep_alive: null,
	lock_keep_alive: false, // quando true, não executa a chamada para atualizar página
	last_request: null,
	user_config: null,
	imap_error: false,
        type: null
};

(function ($) {

	function go_home() {// you are drunk
		window.location = '?box=Inbox&boxfull=INBOX';
	}

	function reload() {
		window.location.reload();
	}

	/*
	 * Exibe diálogo modal.
	 * @returns null
	 */
	function show_modal(obj) {
		if (typeof obj.title == 'undefined' ) obj.title = '';
		$("#modal-content").addClass("active");
		$("#modal-content").html(obj.content);
		toggle_background();

		$("#modal-content").dialog({
			dialogClass: 'dialog_z_index',
			height: 'auto',
			width: 'auto',
			maxWidth: 800,
			title: obj.title,
			beforeClose: function() {
				toggle_background();
			}

		});

		$('#modal-background').on('click', function () {
		   $("#modal-content").dialog("close");
			$("#modal-background").removeClass('active');
			$("#modal-content").html('');
		});
	}

	function close_modal() {
		$('#modal-background').click();
	}

	function toggle_background() {
		var background = $("#modal-background");
		if (background.hasClass('active')) {
			background.removeClass('active');
			return;
		}

		background.addClass('active');

	}

	function close_preview() {
		if ($('div.email_preview').length)
				$('div.email_preview').remove();
	}

	function preview_is_open() {
		return (!!$('div.email_preview').length);
	}
	/*
	 * Habilita/desabilita imagem de loading na parte principal da tela.
	 * @returns null
	 */
	function toggle_stage_loading() {
		if ($('body img.loading').length) {
			$('body img.loading').remove();
		} else {
			$('body').prepend('<img class="loading" src="assets/img/ajax-loader.gif" style="left: 60%; top: 37%; position: absolute; z-index: 100;" />');
		}
	}

	function show_successful() {
		$('body').prepend('<img class="successful" src="assets/img/successful.png" style="left: 60%; top: 37%; position: absolute; z-index: 100;" />');

		setInterval(function(){
			if ($('body img.successful').length) {
				$('body img.successful').remove();
			}
		}, 3000);
	}

	function show_error() {
		$('body').prepend('<img class="error" src="assets/img/delete.png" style="left: 60%; top: 37%; position: absolute; z-index: 100;" />');

		setInterval(function(){
			if ($('body img.error').length) {
				$('body img.error').remove();
			}
		}, 3000);
	}

	// configJSON
	function set_user_config(configJSON) {
		if (!configJSON) return;
		if (!IsJsonString(configJSON)) return;

		var objConfig = JSON.parse(configJSON);
		GLOBALS.user_config = objConfig;

		return !!GLOBALS.user_config; //[!!] turns anything in true or nothing in false;
	}

	// returns user_host_config.json as JS obj
	function load_user_config(email) {
		if (!email) return;

		var configFullFileName = getConfiUserFullFilename(email);
		var sanitizedJSON = '';

		GLOBALS.last_request = $.ajax({
			url: configFullFileName,
			type: 'GET',
			dataType: 'HTML',
			async: false, //must be sync
			data: {
				t: new Date().getTime(),  //INFO::prevent buffered configuration
			}

		}).done(function(data) {
			sanitizedJSON = sanitizeJSON(data);

		}).fail(function(){
			// alert('Não pude carregar as configurações para o email:' + $('input#self_mail').val());

		});

		return sanitizedJSON;
	}

	// ex.: suporte_institutolagosrio_com_br_config.json
	function getConfiUserFullFilename(email) {
		if (!email) return undefined;
		return 'inc/user_config/' + email.replace(/\W+?/g,'_') + '_config.json';
	}

	// INFO:: escapar:
	// : -> \:
	// " -> \"
	// \ -> \\
	function sanitizeJSON(jsonString) {
		if (!jsonString) return;

		var JSON_parts = jsonString.split(",\\\"");
		var last_element = JSON_parts[JSON_parts.length - 1];

		last_element = last_element.replace(/[\"]/g,"'");
		jsonString = '';

		jsonString = jsonString + JSON_parts[0];

		for(var i = 1; i <= JSON_parts.length - 2; i++) {
			jsonString = jsonString + ",\"" + JSON_parts[i];
		}
		last_element = last_element.replace(/\'/, "\"").replace(/'}/, "\"}").replace(/\\/g, "");
		jsonString = jsonString + ",\"" + last_element;
		jsonString = jsonString.replace(/\\\":/g, "\":").replace(/{\\\"/, "{\"").replace(/\":\'/, "\":\"");

		return jsonString;
	}


	//calls load_user_conf, set_user_con
	function initConfig() {
		var email = $('#self_mail').val();
		if (!email) return;

		var configJSON = load_user_config(email);
		if (configJSON) return (set_user_config(configJSON))? true: false;

		return false;
	}

	function retrieve_userConfig() {

		return (GLOBALS.user_config)? GLOBALS.user_config: false;
	}

	/*
	 * Essa função deve ser chamada no callback always de toda requisição sobre email aberto
	   options {
			do_reload => útil para reload em listagem paginada de emails
	   }
	 */
	function move_to_where(options) {
		if (typeof options === 'undefined') options = false;

		$("#modal-background").removeClass('active');

		var user_config = retrieve_userConfig();
		if (user_config.production_mode) {
			var uid_list = GLOBALS.uids_dispose;
			var next_uid, exit_flag, actual_uid = GLOBALS.actual_uid;

			//evita ficar criando flags pelos eventos para definir o que fazer
			if (!actual_uid) reload();

			for(var i = 0; i <= uid_list.length; i++) {
				if (uid_list[i] == actual_uid)
					next_uid = uid_list[i+1];
			}

			//evita de passar undefined para load_email
			if (typeof next_uid == "undefined") reload();

			var mailbox = get_url_param('boxfull');
			close_modal();
			load_email_body(next_uid, mailbox);

		} else {
			(options && options.do_reload)?
				reload():
				go_home();
		}
	}

	//Retorna a lista de uids da table main_list para o modo de produção.
	function get_uids_dispose() {
		var uids_list = [];

		$('.mail_list tr:not(:first)').each(function(){
			return uids_list.push($(this).attr('uid'));
		});

		if (uids_list.length)  return uids_list;
	}

	function set_actual_uid(uid) {
		var uid_to_set = ((typeof uid === 'undefined' )? $('input#uid').val(): uid);

		GLOBALS.actual_uid = uid_to_set;

		return GLOBALS.actual_uid;
	}

	function get_next_uid() {
		var uid = $('div.email_header input#uid').val();

		GLOBALS.last_request = $.ajax({
			url: 'inc/get_next_uid.php',
			type: 'POST',
			data: {
				uid: uid
			},
			dataType: 'json',
			beforeSend: function () {
				toggle_stage_loading();
			}
		}).done(function (data) {
			toggle_stage_loading();

			if (data.error) {
				alert('Não existe próximo email.');
			} else {
				if (data.new_uid) {
					var new_uid = data.new_uid;
					var mailbox = get_url_param('boxfull', get_url_param('box', 'Inbox'));

					load_email_body(new_uid, mailbox);
				}
			}
		});
	}

	function get_prev_uid() {
		var uid = $('div.email_header input#uid').val();

		GLOBALS.last_request = $.ajax({
			url: 'inc/get_prev_uid.php',
			type: 'POST',
			data: {
				uid: uid
			},
			dataType: 'json',
			beforeSend: function () {
				toggle_stage_loading();
			}
		}).done(function (data) {
			toggle_stage_loading();

			if (data.error) {
				alert('Não existe email anterior.');
			} else {
				if (data.new_uid) {
					var new_uid = data.new_uid;
					var mailbox = get_url_param('boxfull', get_url_param('box', 'Inbox'));

					load_email_body(new_uid, mailbox);
				}
			}

		});
	}

	//Set usefull data on GLOBALS
	function set_uids_dispose() {
		GLOBALS.uids_dispose = get_uids_dispose();
		//if (!GLOBALS.uids_dispose.length)  console.log("Can not get uids dispose");
	}
	////////////

	function get_headers(uid) {
		if (!uid)  return;
		var mailbox = 'INBOX.'+get_url_param('box'); //FIXME:: INBOX.Inbox tem que aparecer também na url
		var headers;
                
		GLOBALS.last_request = $.ajax({
			url: 'inc/get_headers.php',
			type: 'POST',
			async: false,
			data: {
				mailbox: mailbox,
				email_uid: uid,
			},
		}).done(function(data) {
			headers = data;
		}).fail(function(e){
                        console.log('uid');
                        console.log(uid);
			console.log(e);

		});

		return (headers)? headers: false;
	}

	/*
	 * Carrega conteúdo de email na tela
		Container -> elemente html que receberá o conteudo do email montado
		callback -> função anônima que executa após respose de dump_body.php
	 */
	function load_email_body(uid, mailbox, container, callback) {
		// Limpa a session caso clique em responder ou encaminhar
		sessionStorage.removeItem('title');
		sessionStorage.removeItem('sender');
		sessionStorage.removeItem('body');
		sessionStorage.removeItem('cc');

		$('div.container ul.top-menu.active').removeClass('active').css('display', 'none');
		$('div.container ul.top-menu.email').addClass('active').css('display', '');
		//for production mode
		set_actual_uid(uid); //set on GLOBALS

		GLOBALS.last_request = $.ajax({
			url: 'inc/dump_body.php',
			type: 'POST',
			data: {
				mailbox: mailbox,
				email_uid: uid
			},
			dataType: 'html',
			beforeSend: function () {
				toggle_stage_loading();
			}
		}).done(function (data) {
			toggle_stage_loading();

			// carrega rascunho na tela de edição
			var box = get_url_param('box', 'Inbox');

			if (box.match(/^drafts$/i)) {
				$('body').append('<div id="draft_content" style="display: hidden;" />');

				var $div = $('div#draft_content');

				$div.html(data);
				var uid = $('#uid').val();
				var title = $div.find('div.email_header h1:first').text();
				var sender = [];
				var cc = [];
				var body = $div.find('div.email-body').html();
				var attachments = [];

				$div.find('div.attachments ul li a').each(function () {
					var $this = $(this);
					attachments.push($this.attr('title').replace(/\s+\([^\)]*\)$/i, ''));
				});
				attachments.join('|');


				$div.find('div.email_header a.mail-to, div.email_header a.mail-from').each(function (i, el) {
					var $this = $(this);
					var name = $this.prev('span.name').text().replace(/['"]/g, '');
					var email = $this.text().replace(/['"]/g, '');
					var user_email = $('#self_mail').val();

					name = name ? name : email;

					if (email != user_email) {
						if (name && email) {
							sender.push({
								label: name,
								value: email
							});
						}
					}
				});
				sender = JSON.stringify(sender);

				$div.find('div.email_header a.mail-cc').each(function (i, el) {
					var $this = $(this);
					var name = $this.prev('span.name').text().replace(/['"]/g, '');
					var email = $this.text().replace(/['"]/g, '');

					name = name ? name : email;

					if (name && email) {
						cc.push({
							label: name,
							value: email
						});
					}
				});
				cc = JSON.stringify(cc);

				sessionStorage.setItem('uid', uid);
				sessionStorage.setItem('type', 'draft');
				sessionStorage.setItem('title', title);
				sessionStorage.setItem('sender', sender);
				sessionStorage.setItem('cc', cc);
				sessionStorage.setItem('body', body);
				sessionStorage.setItem('attachments', attachments);

				// encaminha para página de edição
				$div.remove();
				$('a.opt-new').trigger('click');

				return;
			}

			if (typeof container === 'undefined') {
				container = 'div.stage';
			}

			$(container).html(
				[
					'<div class="printable" style="padding-left: 5px;">',
					data,
					'</div>'
				].join("\n")
			);

			// executa uma função de callback caso seja fornecida
			if (typeof callback === 'function') callback();

			/**
			 * Passar para próximo email e email anterior
			 */
			$('div.email_header img.email_up').on('click', function () {
				get_next_uid();
			});

			$('div.email_header img.email_down').on('click', function () {
				get_prev_uid();
			});

			/*
			 * Adiciona contato
			 */
			$('div.email_header span.email a').on('click', function () {
				var $this = $(this);
				var email = $this.text();
				var name = $this.prev('span.name').text() || email;
				var add = confirm('Adicionar ' + name + ' aos contatos?');

				if (add) {
					GLOBALS.last_request = $.ajax({
						url: 'inc/contact_add.php',
						type: 'POST',
						data: {
							name: name,
							email: email,
							address: '',
							user_email: $('#self_mail').val(),
						}
					}).done(function (data) {
						if (data && data.match(/^Error/i)) {
							alert(data);
						}

						load_contacts();
					});
				}

				return false;
			});

			$('a.mail-to, a.mail-from, a.mail-cc, a.mail-cco').prop('title','Incluir Contato'); // forçando usabilidade
		});

		return false;
	}

	/*
	 * Carrega as pastas do email do server ou do arquivo gerado
	 *  usage::  {
	 *	   load_mailboxes({from_server: true})
	 *	   load_mailboxes({from_file: true})
	 *  }
	 * @returns null
	 */
	function load_mailboxes(retrieve) {
		if (retrieve === undefined) retrieve = false;
		var where = ((retrieve && retrieve.from_server)? 'dump_mailboxes': 'read_mailbox_from_file')

		GLOBALS.last_request = $.ajax({
			url: 'inc/list_mailboxes.php',
			type: 'POST',
			dataType: 'json',
			data: {
				dump_mailboxes: where,
			},
			beforeSend: function() {
				toggle_stage_loading();
			}
		}).done(function (data) {
			toggle_stage_loading();

			var tree = data.mailboxes_tree;
			var mailboxes = data.mailboxes;
			var active = get_url_param('box', 'Inbox');

			$('div.container nav.sidebar .sidebar-pastas').html(traverse(tree, 'lista_pastas')); // Null && null - to not hide links and hide options
			$('#lista_pastas').treeview({ collapsed: true });

			// Expande ul#lista_pastas se o user clicar no + em pastas com hasChildren na raiz
			$("#lista_pastas > li > div[class$='-hitarea']").on('click',function(){
				var $this = $(this);

				if ($this[0].className.match(/expandable/ig) && $("#lista_pastas > li > .collapsable-hitarea").length == 0) {
					$("#lista_pastas").css("height", '');
				} else {
					$("#lista_pastas").css("height", '500');
				 }

			 });

			// FIXME|INFO:: Marca a pasta a tual, se for hasChildren, abre e seta o mailbox_status(total/lidos)
			// isso depende do request de load_emails ser síncrono; o que não é ideal.
			var mailbox_status = GLOBALS.mailbox_status;
			$("ul#lista_pastas").children("li").find("a[data-box='" + get_url_param("boxfull") + "']")
				.css("font-weight", "bold")
				.append(mailbox_status)
				.addClass('mailbox_active')
				.parents('li.expandable')
				.find('div.hitarea')
			.click();
		});
	}
	window.load_mailboxes = load_mailboxes;

	/* Gera Um arquivo JSON para evitar requests desnecessários
	 * @dump_mailboxes [dump_mailboxes] [read_mailbox_from_file]
	 * @no_response false -> cospe o JSON, true -> só gera o file JSON
	 */
	function dump_mailboxes_to_file(obj) {
		 GLOBALS.last_request = $.ajax({
				url: 'inc/list_mailboxes.php',
				type: 'POST',
				data: {
					dump_mailboxes: 'dump_mailboxes',
					no_response: true,
				},
			}).done(function(){
				if(obj.go_home) go_home();
			});
	}

	/*
	 * Carrega emails na página de acordo com a query de busca ('ALL' por padrão).
	 * @param String query
	 * @returns null
	 */
	function load_emails(mailbox, query, page, o_sort) {
		var boxfull = get_url_param('boxfull', 'INBOX');

		if (boxfull.match(/^INBOX.Inbox$/gi)) {
			go_home();
		}

		mailbox = mailbox ? mailbox : get_url_param('box', 'Inbox');
		query = query ? query : get_url_param('query', 'ALL');
		page = page ? page : get_url_param('page', '1');

		if (mailbox.match(/^drafts$/i)) {
			$('div.container ul.top-menu.active').removeClass('active').css('display', 'none');
			$('div.container ul.top-menu.drafts').addClass('active').css('display', '');
		}

		if (mailbox.match(/^trash$/i)) {
			$('div.container ul.top-menu.active').removeClass('active').css('display', 'none');
			$('div.container ul.top-menu.trash').addClass('active').css('display', '');
		}

		if (mailbox.match(/^junk$/i)) {
			$('div.container ul.top-menu.active').removeClass('active').css('display', 'none');
			$('div.container ul.top-menu.spam').addClass('active').css('display', '');
		}

		var sort_query = '';
		var sort_order = '';

		if (o_sort && o_sort.query && o_sort.order) {
			sort_query = o_sort.query;
			sort_order = o_sort.order;
		}

		GLOBALS.last_request = $.ajax({
			async: false,
			url: 'inc/request_to_buffer.php',
			data: {
				where: 'list_emails',
				search_query: query,
				box: mailbox,
				boxfull: boxfull,
				page: page,
				sort_query: sort_query,
				sort_order: sort_order
			},
			type: 'POST',
			dataType: 'json',
			beforeSend: function () {
				toggle_stage_loading();
			},
			error: function (xhr, status, error) {
				if (console) {
					console.error("Error Status: " + status)
					console.error(xhr.statusText);
					console.error(xhr.responseText);
					console.error(xhr.status);
				}
				GLOBALS.imap_error = true;
			}

		}).done(function (data) {
			var box = get_url_param('box', 'Inbox');
			var emails = data.emails;
			var pagination = data.pagination;
			toggle_stage_loading();


                        
			if (!emails.length) {
				//$('div.stage').html('<span class="no-result">Nada encontrado.</span>');
                                
                                $('div.stage').html([
                                    '<table class="mail_list sent_list" cellspacing="0">',
                                    '   <thead>',
                                    '       <tr>',
                                    '           <td style="width: 25px;"><input type="checkbox" class="select_all" /></td>',
                                    '           <td style="width: 50px; text-align: center"><a href="#" id="filtrar"><img src="assets/img/filter01.png" title="Filtrar" /></a></td>',
                                    '           <td data-column="subject" class="subject" style="width: 20%;">Assunto</td>',
                                    '           <td data-column="recipient" class="recipient" style="width: 250px;">Destinatário</td>',
                                    '           <td data-column="relevance" style="width: 50px;">Status</td>',
                                    '           <td></td>',
                                    '           <td></td>',
                                    '           <td data-column="date" class="date">Data</td>',
                                    '       </tr>',
                                    '   </thead>',
                                    '   <tbody>',
                                    '   </tbody>',
                                    '</table>',
                                    '<div style="clear: both;"></div>',
                                    '<div class="usage"><b>' + data.percent_usage + '% de espaço em uso</b> (' + data.quota_info + ')</div>',
                                    '<div class="pagination"></div>'
                                ].join('\n'));
                                
				return false;
			}
                        

                        if (box.match(/^drafts$/i) || box.match(/^sent$/i)) {
                            $('div.stage').html([
                                '<table class="mail_list sent_list" cellspacing="0">',
                                '   <thead>',
                                '       <tr>',
                                '           <td style="width: 25px;"><input type="checkbox" class="select_all" /></td>',
                                '           <td style="width: 50px; text-align: center"><a href="#" id="filtrar"><img src="assets/img/filter01.png" title="Filtrar" /></a></td>',
                                '           <td data-column="subject" class="subject" style="width: 20%;">Assunto</td>',
                                '           <td data-column="recipient" class="recipient" style="width: 250px;">Destinatário</td>',
                                '           <td data-column="relevance" style="width: 50px;">Status</td>',
                                '           <td></td>',
                                '           <td></td>',
                                '           <td data-column="date" class="date">Data</td>',
                                '       </tr>',
                                '   </thead>',
                                '   <tbody>',
                                '   </tbody>',
                                '</table>',
                                '<div style="clear: both;"></div>',
                                '<div class="usage"><b>' + data.percent_usage + '% de espaço em uso</b> (' + data.quota_info + ')</div>',
                                '<div class="pagination"></div>'
                            ].join('\n'));
                        } else {
                            $('div.stage').html([
                                '<table class="mail_list" cellspacing="0">',
                                '   <thead>',
                                '       <tr>',
                                '           <td style="width: 25px;"><input type="checkbox" class="select_all" /></td>',
                                '           <td style="width: 50px; text-align: center"><a href="#" id="filtrar"><img src="assets/img/filter01.png" title="Filtrar" /></a></td>',
                                '           <td data-column="subject" class="subject" style="width: 20%;">Assunto</td>',
                                '           <td data-column="sender" class="sender" style="width: 250px;">Remetente</td>',
                                '           <td data-column="relevance" style="width: 50px;">Status</td>',
                                '           <td></td>',
                                '           <td></td>',
                                '           <td></td>',
                                '           <td data-column="date" class="date">Data</td>',
                                '       </tr>',
                                '   </thead>',
                                '   <tbody>',
                                '   </tbody>',
                                '</table>',
                                '<div style="clear: both;"></div>',
                                '<div class="usage"><b>' + data.percent_usage + '% de espaço em uso</b> (' + data.quota_info + ')</div>',
                                '<div class="pagination"></div>'
                            ].join('\n'));
                        }


			var order_html = '';

			if (data.order_by && data.order) {
                            var $table = $('table.mail_list');

                            if (data.order_by == 'SORTFROM') {
                                    data.order_by = 'sender';
                            } else if (data.order_by == 'SORTTO') {
                                    data.order_by = 'recipient';
                            } else if (data.order_by == 'SORTSUBJECT') {
                                    data.order_by = 'subject';
                            } else if (data.order_by == 'SORTDATE') {
                                    data.order_by = 'date';
                            }

                            $table.attr('data-orderby', data.order_by);
                            $table.attr('data-order', data.order);

                            var class_order = data.order == 'desc' ? 'column-order-down' : 'column-order-up'

                            $('table.mail_list thead tr td').removeClass('column-order-up column-order-down');
                            $('table.mail_list thead tr td.' + data.order_by).append('<div />').addClass(class_order);
			}


			$('div.stage div.pagination').html('').append(pagination);

			for (var i = 0, odd = 0; i < emails.length; ++i, odd = odd ? 0 : 1) { // ?
                            var email = emails[i];
                            var tag = [];

                            var from_tooltip = email.from;
                            var subject_tooltip = '';

                            if (email.from != null) email.from = email.from.replace(/\s+&lt;.*$/i, '').replace(/('|"|&quot;)/g, '');

                            if (email.from != null && email.from.length > 30 ) {
                                    email.from = email.from.substr(0, 27);

                                    if (email.from.match(/\.$/i))
                                            email.from += '..';
                                    else
                                            email.from += '...';
                            }

                            if (box.match(/^drafts$/i) || box.match(/^sent$/i)) {
                                tag = [
                                    '<tr class="' + email.li_class + '" ' + (odd ? 'style="background: #f3f3f3;"' : '') + ' uid="' + email.uid +'">',
                                    '    <td class="mail_selector" style="padding: 1px; width: 25px;"><input type="checkbox" value="' + email.uid + '"></td>',
                                    '    <td style="text-align: center;"><a href="javascript:;" id="alt_favorite" onclick="change_favorite(' + email.uid +');"><img id="alt_img_favorite_' + email.uid + '" src="assets/img/'+ email.has_fav +'.png"  title="Favorito" width="20" /></a></td>',
                                    '    <td class="mail_subject"><a href="javascript:;" uid="' + email.uid + '" number="' + email.number + '">' + email.subject + '</a></td>',
                                    '    <td class="mail_to">' + (email.to ? email.to : '(Sem destinatário)') + '</td>',
                                    '    <td style="width: 100px; text-align: center;"><img src="assets/img/'+email.priority+'.png" style="margin-bottom: -4px" title="'+email.priority+'" width="16" /></td>',
                                    '    <td></td>',
                                    '    <td class="mail_has_att">' + (email.has_reply ? '<img src="assets/img/reply.png" style="margin-bottom: -4px" title="Respondido" width="16" />' : '') + ' ' + (email.has_rw ? '<img src="assets/img/rw.png" style="position: relative; top: 2px;" title="E-mail já foi encaminhado" width="16" />' : '') + '' + (email.has_att ? '<img src="assets/img/attachment.png" title="E-mail com anexo" width="20" style="margin-bottom: -4px; margin-right:5px"/>' : '') + '</td>',
                                    '    <td class="mail_date">' + email.date + '</td>',
                                    '</tr>'
                                ].join('\n');
                            } else {
                                tag = [
                                    '<tr class="' + email.li_class + '" ' + (odd ? 'style="background: #f3f3f3;"' : '') + ' uid="' + email.uid +'">',
                                    '    <td class="mail_selector" style="padding: 1px; width: 25px;"><input type="checkbox" value="' + email.uid + '"></td>',
                                    '    <td style="text-align: center;"><a href="javascript:;" id="alt_favorite" onclick="change_favorite(' + email.uid +');"><img id="alt_img_favorite_' + email.uid + '" src="assets/img/'+ email.has_fav +'.png"  title="Favorito" width="20" /></a></td>',
                                    '    <td class="mail_subject" title="' + subject_tooltip + '"><a href="javascript:;" uid="' + email.uid + '" number="' + email.number + '">' + email.subject + '</a></td>',
                                    '    <td class="mail_from" title="' + from_tooltip + '">' + email.from + '</td>',
                                    '    <td style="width: 100px; text-align: center;"><img src="assets/img/'+email.priority+'.png" style="margin-bottom: -4px" title="'+email.priority+'" width="16" /></td>',                        
                                    '    <td></td>',
                                    '    <td>' + (email.has_reply ? '<img src="assets/img/reply.png" style="margin-bottom: -4px" title="Respondido" width="16" />' : '') + ' ' + (email.has_rw ? '<img src="assets/img/rw.png" style="position: relative; top: 2px;" title="E-mail já foi encaminhado" width="16" />' : '') + '</td>',
                                    '    <td class="mail_has_att">' + (email.has_att ? '<img src="assets/img/attachment.png"  title="E-mail com anexo" width="20" style="margin-bottom: -4px; margin-right:5px" />' : '') + '</td>',
                                    '    <td class="mail_date">' + email.date + '</td>',
                                    '</tr>'
                                ].join('\n');
                            }

                            $('div.stage table.mail_list tbody').append($(tag));
			}
			//get a list of uids disposed on each <tr>
			set_uids_dispose();

			// selecionar todos
			$('div.stage table.mail_list thead :checkbox').on('change', function () {
				var status = $(this).is(':checked');

				$('div.stage table.mail_list tbody :checkbox').each(function (i, el) {
					el.checked = status;
				});
			});

			// ordenar tabela
			$('div.stage table.mail_list thead tr td').on('click', function () {
				var $this = $(this);
				var $table = $('table.mail_list');
				var column = $this.data('column');
				var email_search = $('#email_search').val();
				var search = {
					query: '',
					order: ''
				}

				if (typeof column === 'undefined') return;

				if ($table.data('orderby') == column) {
					search.order = $table.data('order') == 'desc' ? 'asc' : 'desc';
				} else {
					search.order = 'asc';
				}

				switch (column) {
				case 'sender':
					search.query = 'SORTFROM';
					break;

				case 'recipient':
					search.query = 'SORTTO';
					break;

				case 'subject':
					search.query = 'SORTSUBJECT';
					break;

				case 'date':
					search.query = 'SORTDATE';
					break;
				}

				if (email_search) {
					email_search = 'SUBJECT ' + email_search;
				} else {
					email_search = 'ALL';
				}

				load_emails(false, email_search, false, search);
			});

			/*
			 * Click no subject:
			 *	1 click -> preview,
			 *	2 clicks -> fullview
			 */
			(function () {
				var DELAY = 700;
				var clicks = 0;
				var timer = null;

				$('div.stage table.mail_list tr td.mail_subject a').on('click', function () {
					GLOBALS.lock_keep_alive = true; // não carrega a página enquanto o usuário estiver lendo o email.

					var $this = $(this);
					clicks++; //count clicks
					var user_config = retrieve_userConfig();


					if (clicks === 1 && user_config.preview_include) {
						//desmarca emails selecionados
						 $('td.mail_selector input[type=checkbox]:checked').each(function(){
							$(this).prop('checked', '');
						 });

						timer = setTimeout(function () {
							/*
							 * Carrega pré-visualização do email.
							 */
							var uid = $this.attr('uid');
							var mailbox = get_url_param('boxfull', get_url_param('box', 'Inbox'));

							$('<div class="email_preview"></div>').insertAfter('div.stage');

							toggle_stage_loading();

							load_email_body(uid, mailbox, 'div.email_preview', function () {
								$('div.email_preview').find('img.email_up').remove();
								$('div.email_preview').find('img.email_down').remove();
								$('div.email_preview').find('div.email_link_back').remove();

								$('div.email_preview').prepend('<div class="resize_indicator"></div>');

								$('div.email_preview').prepend([
									'<div class="auto_resizer">',
									'		   <div class="small"></div>',
									'		   <div class="medium"></div>',
									'		   <div class="big"></div>',
									'</div>',

								].join("\n"));
								$('div.email_preview').prepend('<img class="preview_close" src="assets/img/delete.png" style="width: 15px; height: 15px; margin: 3px; cursor: pointer;">');

								$('div.email_preview img.preview_close').on('click', function () {
									$('div.email_preview').remove();

									$('div.container ul.top-menu.active').removeClass('active').css('display', 'none');
									$('div.container ul.top-menu.principal').addClass('active').css('display', '');
								});

								if (user_config.preview_redimensionavel) {
									$('div.email_preview').resizable({
										minHeight: 200,
										maxHeight: 680,
										maxWidth: 920,
										minWidth: 675,
										ghost: false,
										handles: 'n, w, nw',
										resize: function() {
											$(this).css('top', '');
											$(this).css('left', '');
										},
									});
								}
								//
								toggle_stage_loading();
							});

							clicks = 0; //after action performed, reset counter
						}, DELAY);

					} else {

						clearTimeout(timer); //prevent single-click action

						/*
						 * Carrega conteúdo de email.
						 */
						var uid = $this.attr('uid');
						var mailbox = get_url_param('boxfull', get_url_param('box', 'Inbox'));

						$('div.email_preview').remove();

						load_email_body(uid, mailbox);

						clicks = 0; //after action performed, reset counter
					}
				})
					.on('dblclick', function (e) {
						e.preventDefault();
					});

			})();

			//setting the fake unseen mails count :)

			var mails_unseen_n = (!data.mails_unseen_n)? '0': data.mails_unseen_n;
			var mails_n = data.mails_n;
			var mailbox_status  = '(<span id="mc">' + mails_n + '</span>/<span id="mcu">' + mails_unseen_n + ' </span>&nbsp;Novas)';

			GLOBALS.mailbox_status = mailbox_status;
		});
	}

	window.load_emails = load_emails;

	/*
	 * Carrega contatos do usuário.
	 * @param String query
	 * @returns null
	 */
	function load_contacts(pattern) {
		if (typeof pattern == undefined) pattern = '.';

		GLOBALS.last_request = $.ajax({
			url: 'inc/contacts_list.php',
			data: {
				pattern: pattern,
				user_email: $("#self_mail").val(),
			},
			type: 'POST',
			dataType: 'json'
		}).done(function (data) {
			var html = [];

			if ($("ul#lista_contatos")) $("ul#lista_contatos").remove(); // No page reload needed.

			if (data.results && data.results.length) {
				html.push('<ul id="lista_contatos">');

				 data.results.sort(function(a, b) { // tem que ver se não vai ficar lento
					 return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
				 });

				for (var i = 0; i < data.results.length; ++i) {
					var original_name = data.results[i].name;
					data.results[i].name = ((data.results[i].name.length >= 13 && data.results[i].name.match(/(^.{0,13})/gi))? RegExp.$1 + '...': data.results[i].name);

					var li = [
						'<li>',
						'   <a href="#" class="contact left" title="'+ data.results[i].email +'" data-email="' + data.results[i].email + '" data-name="' + original_name + '">',
						'	   <img src="assets/img/contact.png"/>',
						'	   <span>' + data.results[i].name + '</span>',
						'   </a>',
						'   <img class="contact_send_mail right" src="assets/img/email.png" title="Enviar email para '+ data.results[i].email +'" width="auto" height="20" />',
						'   <div class="clear"></div>',
						'</li>',
					].join("\n");
					html.push(li);
				}

				html.push('</ul>');
			} else {
				html.push('<span>Nenhum contato</span>');
			}

			$('nav.sub-sidebar.sidebar-contatos').html(html.join('\n'));

			/*
			 * Mail Contact
			 */
			 $('nav.sub-sidebar.sidebar-contatos ul li img.contact_send_mail').on('click', function() {
				var $this = $(this);
				var contact_email = $this.siblings('a.contact').data('email');
				var contact_name = $this.siblings('a.contact').data('name');
				var sender = JSON.stringify([{
					label: contact_name,
					value: contact_email,
				}]);

				if (contact_name && contact_email) {
					sessionStorage.setItem('sender', sender);
				}

				$('ul.top-menu a.opt-new').trigger('click');

			 });


			/*
			 * Tela de edição de contato
			 */
			$('nav.sub-sidebar.sidebar-contatos ul a.contact').on('click', function () {
				var $this = $(this);
				var email = $this.data('email');

				GLOBALS.last_request = $.ajax({
					url: 'inc/contact_info.php',
					data: {
						contact_email: email,
						user_email: $('#self_mail').val(),
					},
					dataType: 'json',
					type: 'POST',
					beforeSend: function () {
						toggle_stage_loading();
					}
				}).done(function (contact) {
					toggle_stage_loading();

					if (contact) {
						show_modal({
							title: 'Contato',
							content: [
								'<div class="contact_info">',
								'   <input type="hidden" name="original_mail" value="' + contact.email + '" />',
								'   <label for="contact_name">Nome</label><br /><input type="text" name="contact_name" id="contact_name" size="60" value="' + contact.name + '" /><br />',
								'   <label for="contact_email">Email</label><br /><input type="text" name="contact_email" id="contact_email" size="60" value="' + contact.email + '" /><br />',
								'   <label for="contact_addr">Endereço</label><br /><input type="text" name="contact_addr" id="contact_addr" size="100" value="' + contact.address + '" /><br />',
								'   <input type="button" name="save" value="Salvar" />',
								'   <input type="button" name="cancelar" value="Cancelar" />',
								'   <input type="button" name="delete" value="Deletar" class="right"/>',
								'   <div class="clear"></div>',
								'</div>'
							].join('\n'),
						});

						$("div.contact_info input[name='delete']").on('click', function () {
							var contact_email = $('input#contact_email').val();

							GLOBALS.last_request = $.ajax({
								url: 'inc/contact_delete.php',
								type: 'POST',
								data: {
									contact_email: contact_email,
									user_email: $("#self_mail").val(),
								},
								beforeSend: function () {
									toggle_stage_loading();
								}
							}).done(function () {
								toggle_stage_loading();
								load_contacts();
								close_modal();
							});
						});

						$("div.contact_info input[name='save']").on('click', function () {
							var original_mail = $('input[name=original_mail]').val();
							var contact_name = $('input#contact_name').val();
							var contact_email = $('input#contact_email').val();
							var contact_addr = $('input#contact_addr').val();

							GLOBALS.last_request = $.ajax({
								url: 'inc/contact_save_info.php',
								type: 'POST',
								dataType: 'JSON',
								data: {
									user_email: $('#self_mail').val(),
									original_mail: original_mail,
									contact_name: contact_name,
									contact_email: contact_email,
									contact_addr: contact_addr
								},
								beforeSend: function () {
									toggle_stage_loading();
								}
							}).done(function (data) {

								toggle_stage_loading();
								if (data) {
									$('input#contact_email').focus();
								}
								load_contacts();
								close_modal();
							});
						});

						$("div.contact_info input[name='cancelar']").on('click', function() {
								close_modal();
						});
					}
				});

				return false;
			});
		});
	}


	/*
	 * Captura parametros da url.
	 * @param String name
	 * @returns String Valor do parâmetro (null caso não encontre).
	 */
	function get_url_param(name, guard) {
		guard = guard !== "undefined" ? guard : null;

		var response = decodeURI(
			(RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [, guard])[1]
		);

		return response === 'null' ? null : response;
	}


	function saveDraft() {
		try {
			var recipients = [];
			var recipients_cc = [];
			var recipients_cco = [];
			var subject = $('div.stage div#email-editor input#subject').val(); //here
			var email_body = $('div.cke_inner iframe').contents().find('body').html();
			var attachments = [];

			$('div#attachments_list span').each(function () {
				attachments.push('uploads/' + $(this).text().replace(/^\s+|\s+$/g, ''));
			});
			attachments = attachments.join('|');

			$('#to li').each(function (i, item) {
				recipients.push($(item).attr('tagvalue'));
			});
			recipients = recipients.join(',');

			$('#cc li').each(function (i, item) {
				recipients_cc.push($(item).attr('tagvalue'));
			});
			recipients_cc = recipients_cc.join(',');

			$('#cco li').each(function (i, item) {
				recipients_cco.push($(item).attr('tagvalue'));
			});
			recipients_cco = recipients_cco.join(',');

			GLOBALS.last_request = $.ajax({
				url: 'inc/save_draft.php',
				type: 'POST',
				dataType: 'json',
				data: {
					recipients: recipients,
					recipients_cc: recipients_cc,
					recipients_cco: recipients_cco,
					subject: subject,
					email_body: email_body,
					attachments: attachments
				}
			}).done(function (data) {
				if (data.draft_uid) {
					var draft_uid = data.draft_uid;

					if (sessionStorage.last_draft_uid) {
						GLOBALS.last_request = $.ajax({
							url: 'inc/mail_delete.php',
							type: 'POST',
							data: {
								permanent: true,
								maillist: sessionStorage.last_draft_uid,
								boxfull: 'INBOX.Drafts'
							},
							dataType: 'json'
						}).done(function (data) {
							sessionStorage.setItem('last_draft_uid', draft_uid);
						});
					} else {
						sessionStorage.setItem('last_draft_uid', draft_uid);
					}
				}
			});
		} catch (err) {
			// usuário não visualiza esse erro
			console.log(err);
		}
	}

	$(document).ready(function () {
		// keep imap connection alive
		GLOBALS.cron_keep_alive = setInterval(function () {
			GLOBALS.last_request = $.ajax({
				url: 'inc/imap_connection.php',
				type: 'GET'
			});

			if (!GLOBALS.lock_keep_alive && !GLOBALS.imap_error) {
				load_emails(false, false, false);
			}

		}, 1000 * 60 * 5);

		// sidebar redimensionável
		$('nav.sidebar').resizable({
			handles: 'n',
			ghost: false,
			minWidth: 197,
			maxHeight: 500,
		});

		//tooltip para coonfigurações;
		$(document).tooltip({
			hide: {effect: "blind", duration: 70},
			show:{ effect: "blind", duration: 70 },
		});

		/*
		 * Carrega pastas e lista de emails.
		 */
		(function () {
			load_contacts();
			load_emails(false, false, false);
			load_mailboxes(); // INFO:: temporário?
			initConfig();
		})();

		/*
		 * Checa se o browser suporta 'placeholder' nativamente,
		 * se não, implementa em jQuery.
		 */
		(function () {
			jQuery.support.placeholder = (function () {
				var i = document.createElement('input');
				return 'placeholder' in i;
			})();

			if (!$.support.placeholder) {
				$('[placeholder]').focus(function () {
					var $input = $(this);
					if ($input.val() === $input.attr('placeholder')) {
						$input.val('');
						$input.removeClass('placeholder');
					}
				}).blur(function () {
					var $input = $(this);
					if ($input.val() === '' || $input.val() === $input.attr('placeholder')) {
						$input.addClass('placeholder');
						$input.val($input.attr('placeholder'));
					}
				}).blur();
			}
		})();

		// Checa se o usuário tem arquivo de configuração
		// Se não existir arquivo de configuração
		// Mostra um balão para criação
		(function() {
			var configDoesNexists = retrieve_userConfig();

			if (configDoesNexists) return;

			var balloonHtml = [
			'<div style="width: 200px; height: 105px;">',
			'   Você ainda não escolheu suas preferencias de usabilidade nem criou uma assinatura de email<br/><p>Clique aqui para escolher suas preferencias.<br/>Obrigado!</p>',
			'</div>',
			].join("\n");

			$('.config-button').balloon({
				contents: balloonHtml,
				position: 'bottom',
			});
			$('.config-button').first().mouseover();

			setTimeout(function(){
				$('.config-button').hideBalloon();
			}, 9000);


		})();

		////EVENTS////
                
		/*
		 * Exibe apenas favoritos ou todos.
		 */
                $('#filtrar').on('click', function()
                {
                    $.ajax({
                        url: 'inc/filter_favorite.php',
                        type: 'POST',
                        dataType: 'json'
                    });
                    setTimeout(function(){
                        reload();
                    }, 1000);
                });
                
		/*
		 * Imprime email
		 */
		$('ul.top-menu a.opt-print').on('click', function () {
			if (preview_is_open()) {
				close_preview();
				alert('Não é possível imprimir o email no preview, clique duas vezes para abrir o email então imprima.');

				return;
			}

			var hide_attachments = !confirm("Incluir anexos na impressão?") ? 'div.attachments{display: none !important;}' : '';
			var title = $('.email_header > h1:nth-child(2)', 'div.stage').text();

			var p_content = [
				'<!DOCTYPE HTML>',
				'<html>',
				'   <head>',
				'	   <title>' + title + '</title>',
				'	   <style type="text/css">@media print{body{width:800px;height:100%;margin:0;padding:0;font-size:13px;font-family:Tahoma,Geneva,sans-serif}div.email_header{width:90%;color:#2b2b2b;margin-bottom:30px}div.email_header h1{font-size:16px}div.email_header span{margin-right:20px}div.email_header span.date{float:right;text-align:right;font-weight:700}div.email_header span.time{float:right;text-align:right;font-weight:700}div.email_header div.header-separator{width:100%;padding:5px 0;margin-bottom:20px;border-bottom:2px solid #595959}div.email_header span.email span{margin:0}div.pagination{padding:10px;position:fixed;bottom:38px}div.pagination a{padding:2px 5px;margin:2px;text-decoration:none;color:#2f75fa}div.pagination a:hover,div.pagination a:active{border-bottom:1px solid #2f75fa;color:#000}div.pagination span.current{padding:2px 5px;margin:2px;border:1px solid #2f75fa;font-weight:700;background-color:#2f75fa;color:#fff}div.pagination span.disabled{padding:2px 5px;margin:2px;color:#000}div.email_header div.attachments{margin:10px 0 -16px}div.email_header div.attachments ul{margin:0;padding:0;width:800px}div.email_header div.attachments ul li{list-style:none;margin-left:auto;margin-right:auto;display:inline-block}div.email_header div.attachments ul li a{display:inline-block;text-decoration:none;color:#000;text-align:center}div.email_header div.attachments ul li a img{float:left;width:100px;width:100px}div.email_header div.attachments ul li a p{clear:both;font-size:12px;font-family:Tahoma,Geneva,sans-serif}' + hide_attachments + '}</style>',
				'   </head>',
				'   <body>',
				$('div.stage').html(),
				'   </body>',
				'</html>'
			].join('\n');

			window.document.write(p_content);
			window.print();
			reload();
		});


		/*
		 * Deleta os emails selecionados
		 */
		$('ul.top-menu a.opt-delete').on('click', function () {
			var uid = $('input[type=hidden]#uid').val();
			var uids = [];
			var flag_stayOnPage = false;

			if (preview_is_open()) {
				close_preview();
				alert('Selecione um ou mais emails ou abra o email que desejar excluir.');
				return;
			}

			// se uid for vazio, utilizar ids das mensagens selecionadas
			if (uid) {
				uids.push(uid);
			} else {
				flag_stayOnPage = true;
				$('td.mail_selector input[type=checkbox]:checked').each(function (i, element) {
					var $this = $(element);
					uids.push($this.val());
				});
			}

			var mailbox = get_url_param('box', 'INBOX');
			var boxfull = get_url_param('boxfull', 'INBOX');
			var query = get_url_param('query', 'ALL');
			var page = get_url_param('page', '1');
			var permanent = mailbox.match(/^(trash|junk)$/i) ? '1' : '0';
			toggle_stage_loading();
			GLOBALS.last_request = $.ajax({
				url: 'inc/mail_delete.php',
				type: 'POST',
				data: {
					permanent: permanent,
					maillist: uids.join(','),
					box: mailbox,
					boxfull: boxfull
				},
				dataType: 'json',
				beforeSend: function() {
					close_modal();
					toggle_stage_loading();
				},
			}).done(function (data) {

				if (data.errors) {
					alert(data.errors + ' emails não foram excluidos.');
					return;
				}

				//refresh mails count
				$("#mc").html($("#mc").html() - uids.length);

				show_successful();

				(flag_stayOnPage)?
					load_emails():
					move_to_where()
			});

			return false;
		});


		/**
		 * Move o email aberto atualmente, ou emails selecionados
		 */
		$('ul.top-menu a.opt-move').on('click', function () {
			close_preview(); //precisa fechar o preview se o modo de produção estiver setado

			var uid_list_exists = (!sessionStorage.uids)? !!$('td.mail_selector input[type=checkbox]:checked').size(): !!sessionStorage.uids;
			var uid_exists = !!$('input[type=hidden]#uid').val();

			if (!(uid_list_exists || uid_exists)) {  //Previne o modo de produção de mover para o próximo email caso tente clicar no botão de mover com o preview aberto
				alert('Selecione um ou mais emails ou abra o email que desejar mover.');
				return;
			}

			show_modal({
				title: 'Mover Para',
				content: [
					'<div class="move_mailbox_content">',
					'<div class="mailboxes_container"></div>  ',
					'</div>',
				].join("\n"),
			});

			GLOBALS.last_request = $.ajax({
				url: 'inc/list_mailboxes.php',
				type: 'POST',
				dataType: 'json',
				data: {
					dump_mailboxes: 'read_mailbox_from_file'
				},

			}).done(function (data) {
				var tree = data.mailboxes_tree;
				var mailboxes = data.mailboxes;
				var active = get_url_param('box', 'Inbox');

				$('.mailboxes_container').html(
					traverse(tree, 'folder_list', true) //true for hidelinks && null to hide options
				);

				$('#folder_list').treeview({ collapsed: true });

				$('#folder_list li a').on('click', function () {
					var $this = $(this);
					var uid = $('input[type=hidden]#uid').val();
					var uids = [];
					var target = $this.data('box');
					var flag_stayOnPage = false;

					// se uid for vazio, utilizar ids das mensagens selecionadas
					if (uid) {
						uids.push(uid);
					} else {
						if (!sessionStorage.uids) {
							$('td.mail_selector input[type=checkbox]:checked').each(function (index, element) {
								var $this = $(element);
								uids.push($this.val());
							});
						} else {
							uids = sessionStorage.uids.split(',');
						}
						flag_stayOnPage = true;
						sessionStorage.removeItem('uids');
					}

					var mailbox = get_url_param('boxfull');
					var query = get_url_param('query', 'ALL');
					var page = get_url_param('page', '1');

					GLOBALS.last_request = $.ajax({
						url: 'inc/mail_move.php',
						type: 'POST',
						data: {
							uid: uids.join('|'),
							target:  target,
							mailbox: mailbox,
						},
						beforeSend: function() {
							close_modal();
						},

					}).done(function() {
						show_successful();

					}).fail(function(){
						show_error();

					}).always(function(){
						if (flag_stayOnPage) {
							load_emails();
						} else {
							move_to_where({do_reload: true});
						}

					});

				});
			});

			return false;
		});

		/**
		 * Marca como lido
		 */
		$('ul.top-menu a.opt-mark-read').on('click', function () {
			var $this = $(this);
			var uid = $('input[type=hidden]#uid').val();
			var uids = [];
			var flag_stayOnPage = false;

			// se uid for vazio, utilizar ids das mensagens selecionadas
			if (uid) {
				uids.push(uid);
			} else {
				$('td.mail_selector input[type=checkbox]:checked').each(function (index, element) {
					//FIXME:: Deixar mais legível
					if ($(this).parent().parent().attr('class') === "unread") {
						var $this = $(element);
						uids.push($this.val());

						flag_stayOnPage = true; // FIXME:: validar se uids.lenght != 0
					}
				});

			}

			var mailbox = get_url_param('boxfull', 'INBOX');
			var query = get_url_param('query', 'ALL');
			var page = get_url_param('page', '1');

			GLOBALS.last_request = $.ajax({
				url: 'inc/mail_seen.php',
				type: 'POST',
				data: {
					uids: uids.join('|'),
					mailbox: mailbox
				}
			}).done(function (data) {
				if (uid) {
					load_contacts();
					load_mailboxes();
				} else {
					show_successful();

					if (flag_stayOnPage) {
						$("#mcu").html($("#mcu").html() - uids.length);
						load_emails();
					}
				}
			});

			return false;
		});


		/**
		 * Marca como não lido
		 */
		$('ul.top-menu a.opt-mark-unread').on('click', function () {
			var $this = $(this);
			var uid = $('input[type=hidden]#uid').val();
			var uids = [];
			var flag_stayOnPage = false;

			// se uid for vazio, utilizar ids das mensagens selecionadas
			if (uid) {
				uids.push(uid);
			} else {
				$('td.mail_selector input[type=checkbox]:checked').each(function (index, element) {
			// FIXME:: deixar mais claro
					if ($(this).parent().parent().attr('class') === "read") {
						var $this = $(element);
						uids.push($this.val());

						flag_stayOnPage = true; // FIXME:: validar se uids.lenght != 0
					}
				});
			}

			var mailbox = get_url_param('boxfull', 'INBOX');
			var query = get_url_param('query', 'ALL');
			var page = get_url_param('page', '1');

			GLOBALS.last_request = $.ajax({
				url: 'inc/mail_unseen.php',
				type: 'POST',
				data: {
					uids: uids.join('|'),
					mailbox: mailbox
				}
			}).done(function (data) {
				if (uid) {
					load_contacts();
					load_mailboxes();
				} else {
					if (flag_stayOnPage) {
						$("#mcu").html(parseInt($("#mcu").html()) + uids.length);
						load_emails();
					}
				}
			});

			return false;
		});



		/**
		 * Restaura email para a caixa de entrada
		 */
		$('ul.top-menu a.opt-restore').on('click', function () {
			var uid = $('input[type=hidden]#uid').val();
			var uids = [];

			// se uid for vazio, utilizar ids das mensagens selecionadas
			if (uid) {
				uids.push(uid);
			} else {
				$('td.mail_selector input[type=checkbox]:checked').each(function (index, element) {
					var $this = $(element);
					uids.push($this.val());
				});
			}

			var mailbox = get_url_param('boxfull', 'INBOX');
			var query = get_url_param('query', 'ALL');
			var page = get_url_param('page', '1');

			GLOBALS.last_request = $.ajax({
				url: 'inc/mail_move.php',
				type: 'POST',
				data: {
					uid: uids.join('|'),
					target: 'INBOX',
					mailbox: mailbox
				}
			}).done(function (data) {
				go_home();
			});

			return false;
		});

		/**
		 * Marca como spam
		 */
		$('ul.top-menu a.opt-spam').on('click', function () {
			var uid = $('input[type=hidden]#uid').val();
			var uids = [];

			// se uid for vazio, utilizar ids das mensagens selecionadas
			if (uid) {
				uids.push(uid);
			} else {
				$('td.mail_selector input[type=checkbox]:checked').each(function (index, element) {
					var $this = $(element);
					uids.push($this.val());
				});
			}

			if(uids.length == 0) {
				alert("Selecione um ou mais email(s) para marcar como Spam");
				return;
			}

			var mailbox = get_url_param('boxfull', 'INBOX');
			var query = get_url_param('query', 'ALL');
			var page = get_url_param('page', '1');

			GLOBALS.last_request = $.ajax({
				url: 'inc/mail_move.php',
				type: 'POST',
				data: {
					uid: uids.join('|'),
					target: 'INBOX.Junk',
					mailbox: mailbox
				}
			}).done(function (data) {
				load_emails();
			});

			return false;
		});



		/*
		 * Limpa lixeira e caixa de spam
		 */
		$('ul.top-menu a.opt-empty').on('click', function () {
			var mailbox = get_url_param('box', 'INBOX');
			var boxfull = get_url_param('boxfull', 'INBOX');
			var query = get_url_param('query', 'ALL');
			var page = get_url_param('page', '1');

			if (mailbox.match(/^(trash|junk)$/i)) {
				GLOBALS.last_request = $.ajax({
					url: 'inc/mail_erase_box.php',
					type: 'POST',
					data: {
						boxfull: boxfull
					}
				}).done(function (data) {
					load_contacts();
					load_mailboxes();
					load_emails(mailbox, query, page);
				});
			}

			return false;
		});



		/*
		 * Tela de novo email
		 */
		$('ul.top-menu a.opt-new').on('click', function () {
			GLOBALS.lock_keep_alive = true;

			// Evita que uma requisição ajax em andamento prejudique a tela de novo email
			if (GLOBALS.last_request) {
				GLOBALS.last_request.abort();
				GLOBALS.last_request = null;

				if ($('body img.loading').length) {
					$('body img.loading').remove();
				}
			}

			$('div.container ul.top-menu.active').removeClass('active').css('display', 'none');
			$('div.container ul.top-menu.new').addClass('active').css('display', '');

			var mailbox = get_url_param('box', 'INBOX');
			var boxfull = get_url_param('boxfull', 'INBOX');
			var query = get_url_param('query', 'ALL');
			var page = get_url_param('page', '1');
			var uid = sessionStorage.uid ? sessionStorage.uid : 0;

			var prefix = '';
                                                
			var email_action = sessionStorage.type;

			switch (sessionStorage.type) {
			case 'draft':
				prefix = '';
				break;

			case 'forward':
				prefix = 'FWD: ';
				break;

			case 'response':
			default:
				prefix = 'RE: ';
			}


			var  html_to_stage = [
				'<input type="hidden" id="message_type" value="'+ prefix.replace(/\s+|:/i, '').toLowerCase() +'" />',
				'<div id="email-editor" style="margin: 0 auto;"></div>',
			].join("\n");

			var user_config = retrieve_userConfig();
			var assinatura = (user_config.include_assinatura? strRepeat('<br/>', 4) + user_config.assinatura: '');

			var html_to_editor = [
				'<div class="left">',
				'   <label for="subject">Assunto: </label>',
				'   <input type="text" name="subject" id="subject" size="120" value="' + (sessionStorage.title ? prefix + sessionStorage.title : '') + '" /><br />','<input type="button" id="send" value="Enviar" />',
				'</div>',
				'<div class="right">',
				'   <span>Relevancia</span>',
				'   <select name="relevancia" id="relevance" class="left">',
				'	   <option value="normal">Normal</option>',
				'	   <option value="important">Importante</option>',
				'	   <option value="urgent">Urgente</option>',
				'   </select>',
				'</div>',
				'<div class="clear"></div>',
				'<textarea class="ckeditor" name="email_body" id="email_body">',
					assinatura,
					sessionStorage.body? sessionStorage.body: '',
				'</textarea>',
			].join("\n");

			// limpando tela
			$('div.stage').html(html_to_stage);
			$('#email-editor').append(html_to_editor);

			var attachments = '';

			if (sessionStorage.attachments) {
				var atts = sessionStorage.attachments.split('|');
				for (var i = 0; i < atts.length; ++i) {
					atts[i] = '<span class="att_item">' + atts[i] + '<img src="assets/img/delete.png" width="10" height="10"></span>';
				}

				attachments = atts.join("\n<br />\n");
			}

			$('nav.sidebar').html([
				'<label for="to">Para </label><br />',
				'<ul id="to" style="width: 93%;" multiple></ul>',

				'<label for="cc">Cc </label><br />',
				'<ul id="cc" style="width: 93%;" multiple></ul>',

				'<label for="cco">Cco </label><br />',
				'<ul id="cco" style="width: 93%;" multiple></ul>',

				'<form action="inc/enviar_arquivo.php" name="formUpload" id="formUpload" method="post" enctype="multipart/form-data">',
				'	<label>Anexos <br /><input type="file" name="attachment[]" id="attachment" size="45" multiple /></label>',
				'	<br />',
				'	<progress value="0" max="100"></progress><span id="porcentagem">0%</span>',
				'</form>',
				'<br />',
				'<div id="attachments_list">' + attachments + '</div>'
			].join('\n'));

			$('div#attachments_list span img').on('click', function () {
				var $this = $(this);
				$this.parent('span.att_item').next('br').remove();
				$this.parent('span.att_item').remove();
			});

			// anexos (jQuery.form upload)
			$('input#attachment').on('change', function () {
				$('#formUpload').ajaxForm({
					uploadProgress: function (event, position, total, percentComplete) {
						$('progress').attr('value', percentComplete);
						$('#porcentagem').html(percentComplete + '%');
					},
                                        success: function (data) {
                                            $('progress').attr('value', '100');
                                            $('#porcentagem').html('100%');

                                            for(var i = 0; i < data.length; i++) {
                                                if (data[i].sucesso == true) {
                                                    $('div#attachments_list').append('<span class="att_item"><a target="_blank" href="./inc/uploads/'+ data[i].filepath.replace(/^uploads\//, '') +'">' + data[i].filepath.replace(/^uploads\//, '') + '</a><img src="assets/img/delete.png" width="10" height="10" /></span><br />');

                                                    $('progress').attr('value', '0');
                                                    $('#porcentagem').html('0%');

                                                    $('div#attachments_list span img').on('click', function () {
                                                        var $this = $(this);
                                                        $this.parent('span.att_item').next('br').remove();
                                                        $this.parent('span.att_item').remove();
                                                    });
                                                } else {
                                                    console.log('Fail: ' + data[i].filepath);
                                                }
                                            }
                                        },
					error: function () {
						$('#resposta').html('Erro ao enviar requisição!');
					},
					dataType: 'json',
					url: 'inc/enviar_arquivo.php',
					resetForm: true
				}).submit();

			});

			// jQuery Tagit nos campos de destinatário, cópia e cópia oculta
			// PS: destinatário fica separado para receber email no caso de resposta.
			$('nav.sidebar #to').tagit({
				tagsChanged: function () {
					$('li.tagit-choice').each(function (i, el) {
						var $el = $(el);
						if (!$el.attr('tagvalue')) {
							$el.attr('tagvalue', $el.find('.tagit-label').text());
							$el.removeAttr('tagit-type-none');
							$el.removeClass('tagit-type-none');
						}
					});
				},
				minLength: 3,
				initialTags: (sessionStorage.sender ? JSON.parse(sessionStorage.sender) : []),
				tagSource: function (request, response) {
					GLOBALS.last_request = $.ajax({
						url: 'inc/contacts_list.php',
						type: 'POST',
						dataType: 'json',
						data: {
							pattern: request.term.split(/,\s*/).pop(),
							user_email: $("#self_mail").val(),
						},
						success: function (data) {
							if (!data) return;
							response($.map(data.results, function (contact) {
								return {
									label: contact.name,
									value: contact.email
								};
							}));
						}
					});
				}
			});

			$('nav.sidebar #cc').tagit({
				minLength: 3,
				initialTags: (sessionStorage.cc ? JSON.parse(sessionStorage.cc) : []),
				tagSource: function (request, response) {
					GLOBALS.last_request = $.ajax({
						url: 'inc/contacts_list.php',
						type: 'POST',
						dataType: 'json',
						data: {
							pattern: request.term.split(/,\s*/).pop(),
							user_email: $("#self_mail").val(),
						},
						success: function (data) {
							if (!data) return;
							response($.map(data.results, function (contact) {
								return {
									label: contact.name,
									value: contact.email
								};
							}));
						}
					});
				},
				tagsChanged: function () {
					$('li.tagit-choice').each(function (i, el) {
						var $el = $(el);
						if (!$el.attr('tagvalue')) {
							$el.attr('tagvalue', $el.find('.tagit-label').text());
							$el.removeAttr('tagit-type-none');
							$el.removeClass('tagit-type-none');
						}
					});
				},
			});

			$('nav.sidebar #cco').tagit({
				minLength: 3,
				tagSource: function (request, response) {
					GLOBALS.last_request = $.ajax({
						url: 'inc/contacts_list.php',
						type: 'POST',
						dataType: 'json',
						data: {
							pattern: request.term.split(/,\s*/).pop(),
							user_email: $("#self_mail").val(),
						},
						success: function (data) {
							if (!data) return;
							response($.map(data.results, function (contact) {
								return {
									label: contact.name,
									value: contact.email
								};
							}));
						}
					});
				},
				tagsChanged: function () {
					$('li.tagit-choice').each(function (i, el) {
						var $el = $(el);
						if (!$el.attr('tagvalue')) {
							$el.attr('tagvalue', $el.find('.tagit-label').text());
							$el.removeAttr('tagit-type-none');
							$el.removeClass('tagit-type-none');
						}
					});
				},
			});

                        $('div.stage #email-editor input[type=button]#send').on('click', function () {
                            var recipients = [];
                            var recipients_cc = [];
                            var recipients_cco = [];
                            var subject = $('div.stage div#email-editor input#subject').val();
                            var email_body = $('div.cke_inner iframe').contents().find('body').html();
                            var attachments = [];

                            $('div#attachments_list span').each(function () {
                                var $this = $(this);
                                var filename = $this.text();
                                var ext;

                                filename = filename.replace(/^\s+?|\s+?$/,'');
                                if (filename.match(/\.([a-z0-9]+)$/i)) {
                                   ext = RegExp.$1;
                                   filename = filename.replace(/\.[a-z0-9]+$/i, '');
                               }

                               filename += '.';
                               filename += ext;
                               attachments.push('uploads/'+filename);
                            });

				attachments = attachments.join('|');

				$('#to li').each(function (i, item) {
					recipients.push($(item).attr('tagvalue'));
				});
				recipients = recipients.join(',');

				$('#cc li').each(function (i, item) {
					recipients_cc.push($(item).attr('tagvalue'));
				});
				recipients_cc = recipients_cc.join(',');

				$('#cco li').each(function (i, item) {
					recipients_cco.push($(item).attr('tagvalue'));
				});
				recipients_cco = recipients_cco.join(',');

				var arr_fields = [recipients, recipients_cc, recipients_cco];
				var flag_to_continue = false;
				for (var field in arr_fields) {
					if (arr_fields[field].length) {
						flag_to_continue = true;
					}
				}

				var relevance = $("#relevance").val();

				if (!flag_to_continue) {
					alert("Por favor especifique para qual, ou quais, endereço(s) será destinado este email.");
					return;
				}

				GLOBALS.last_request = $.ajax({
					url: 'inc/sendmail.php',
					type: 'POST',
					dataType: 'JSON',
					data: {
						recipients: recipients,
						recipients_cc: recipients_cc,
						recipients_cco: recipients_cco,
						subject: subject,
						email_body: email_body,
						attachments: attachments,
						relevance: relevance,
                                                uid: uid,
                                                action: $('#message_type').val(),
                                                acao: GLOBALS.type
					},
					beforeSend: function() {
						toggle_background();
					}
				}).done(function (data, status, xhr) {
                                        /*
					// salva email na tabela de respondido ou encaminhado
					if (uid != 0) {
						GLOBALS.last_request = $.ajax({
							url: 'inc/register_reply.php',
							async: false,
							data: {
								uid: uid,
								action: $('#message_type').val()
							},
							type: 'POST',
							dataType: 'json'
						});
					}
                                        */
                                       
					if (data.match(/success/ig)) {
						toggle_background();

						var user_config = retrieve_userConfig();
						if (user_config.production_mode) {
							$('.sidebar').html('');
							$('.sidebar').append(window.sidebar);
						}

						move_to_where();

					}
				}).fail(function (data, status, xhr) {
                                    //alert('Erro ao enviar email.');
				});
                                // <Corrigir>, não é necessário reload. Done e Fail, mesmo a função retornando success está entrando em fail.
                                window.setTimeout('location.reload()', 1000);
                                GLOBALS.type = '';
			});

			$('div.stage #email-editor input[type=button]#cancel').on('click', function () {
				go_home();
			});

			CKEDITOR.replace('email_body', {
				toolbar: null,
			});
			CKEDITOR.config.height = 500;

			// delete sessionStorage values
			var last_draft_uid = sessionStorage.last_draft_uid;
			sessionStorage.clear();
			sessionStorage.setItem('last_draft_uid', last_draft_uid);
			sessionStorage.setItem('email_action', email_action);

			// tarefa para salvar rascunho a cada 5 minutos
			GLOBALS.cron_save_draft = setInterval(saveDraft, 1000 * 60 * 5);

			// botão de salvar rascunho
			$('ul.top-menu a.opt-save-draft').on('click', function () {
				saveDraft();
			});

			return false;
		});

		function prepare_body_header(uid) {
			if (!uid) return;

			var headers = get_headers(uid);
			//var date = headers.MailDate;

			var data_header = {
				sender_address: headers && headers.senderaddress? headers.senderaddress: '',
				date: headers.date? headers.date: '',
				subject: headers && headers.subject? headers.subject: '',
				to_address: headers && headers.toaddress? headers.toaddress: '',
			}

			var html_parsed_header = [
				strRepeat('<br />',3),
				'<blockquote style="margin:0px 0px 0px 0.8ex;border-left-width:1px;border-left-color:rgb(204,204,204);border-left-style:solid;padding-left:1ex"><br />',
				'	De: ' + data_header.sender_address + '<br />',
				'	Data: ' + data_header.date + '<br />',
				'	Assunto: ' + data_header.subject + '<br />',
				'	Para: ' + data_header.to_address + '<br />',
					$('div.email-body').html(),
				'	</blockquote>',
			].join("\n");

			return html_parsed_header;
		 }

		/*
		 * Encaminha email
		 */
		$('ul.top-menu a.opt-forward').on('click', function () {
			window.sidebar = $(".sidebar").html();

			var name = $('div.email_header a.mail-from').prev('span.name').text().replace(/['"]/g, '');
			var email = $('div.email_header a.mail-from').text().replace(/['"]/g, '');
			var title = $('div.email_header h1:first').text();
			var uid = $('#uid').val();
			var attachments = [];
                        GLOBALS.type = 'FOR';

			$('div.attachments ul li a').each(function () {
				var $this = $(this);
				attachments.push($this.attr('title').replace(/\s+\([^\)]*\)$/i, ''));
			});
			attachments = attachments.join('|');

			name = name ? name : email;

			var sender = JSON.stringify([{
				label: name,
				value: email
			}]);

			var body = prepare_body_header(uid);

			sessionStorage.setItem('uid', uid);
			sessionStorage.setItem('type', 'forward');
			sessionStorage.setItem('title', title);
			sessionStorage.setItem('body', body);
			sessionStorage.setItem('attachments', attachments);

			close_preview();
			$('ul.top-menu a.opt-new').trigger('click');
		});

		/*
		 * Responder email
		 */
		$('ul.top-menu a.opt-response').on('click', function () {
			window.sidebar = $(".sidebar").html();

			var name = $('div.email_header a.mail-from').prev('span.name').text().replace(/['"]/g, '');
			var email = $('div.email_header a.mail-from').text().replace(/['"]/g, '');
			var title = $('div.email_header h1:first').text();
			var uid = $('#uid').val();
                        GLOBALS.type = 'RE';
                        
			name = name ? name : email;

			var sender = JSON.stringify([{
				label: name,
				value: email
			}]);

			var body = prepare_body_header(uid);
                        
			sessionStorage.setItem('uid', uid);
			sessionStorage.setItem('title', title);
			sessionStorage.setItem('body', body);
			// sessionStorage.setItem('attachments', attachments);

			if (name && email) {
				sessionStorage.setItem('sender', sender);
			}

			close_preview();
			$('ul.top-menu a.opt-new').trigger('click');
		});

		$('ul.top-menu a.opt-response-all').on('click', function () {
			window.sidebar = $(".sidebar").html();

			var uid = $('#uid').val();
			var title = $('div.email_header h1:first').text();
			var sender = [];
			var cc = [];
			var body = prepare_body_header(uid);


			$('div.email_header a.mail-to, div.email_header a.mail-from').each(function (i, el) {
				var $this = $(this);
				var name = $this.prev('span.name').text().replace(/['"]/g, '');
				var email = $this.text().replace(/['"]/g, '');
				var user_email = $('#self_mail').val();

				name = name ? name : email;

				if (email != user_email) {
					if (name && email) {
						sender.push({
							label: name,
							value: email
						});
					}
				}
			});
			sender = JSON.stringify(sender);

			$('div.email_header a.mail-cc').each(function (i, el) {
				var $this = $(this);
				var name = $this.prev('span.name').text().replace(/['"]/g, '');
				var email = $this.text().replace(/['"]/g, '');

				name = name ? name : email;

				if (name && email) {
					cc.push({
						label: name,
						value: email
					});
				}
			});
			cc = JSON.stringify(cc);

			sessionStorage.setItem('uid', uid);
			sessionStorage.setItem('title', title);
			sessionStorage.setItem('sender', sender);
			sessionStorage.setItem('cc', cc);
			sessionStorage.setItem('body', body);
			// sessionStorage.setItem('attachments', attachments);

			close_preview();
			$('ul.top-menu a.opt-new').trigger('click');
		});
		// opt-new interactions end

		/*
		 * Procura emails por remetente, título e conteúdo.
		 */
		$('#email_search').on('keyup', function (event) {
			var $this = $(this);
			var content = $this.val();

			var mailbox = get_url_param('box', 'Inbox');
			var page = get_url_param('page', '1');


			var key = event.which+''; //turns type NUMBER to STRING
			if (key.match(/8|65|90|96|105/)) { // só faz a busca se a tecla pressionada estiver no intervalo de a-z.
				if (content) {
					var query = content
					// function load_emails(mailbox, query, page, o_sort)
					load_emails(mailbox, content, page, {query: 'SORTFROM', order: 'asc'});
				}
			}

			if (!content) {
				load_emails(mailbox, 'ALL', page);
			}
		});

		/*
		 * Procura contatos por nome e email.
		 */
		$('#contact_search').on('keyup', function (key) {

			var $this = $(this);
			var content = $this.val();

			if (key.keyCode != 13 && content != "") return;
			if (content) {
				load_contacts(content);
			} else {
				load_contacts();
			}


		});


		/*
		 * Adicionar contato
		 */
		$('nav.contatos a.add_contact').on('click', function () {
			show_modal({
				title: 'Adicionar contato',
				content: [
					'<div id="title"></div>',
					'<div class="contact_info">',
					'   <input type="hidden" name="original_mail" value="" />',
					'   <label for="contact_name">Nome</label><br /><input type="text" name="contact_name" id="contact_name" size="60" value="" /><br />',
					'   <label for="contact_email">Email</label><br /><input type="text" name="contact_email" id="contact_email" size="60" value="" /><br />',
					'   <label for="contact_addr">Endereço</label><br /><input type="text" name="contact_addr" id="contact_addr" size="100" value="" /><br />',
					'   <input type="button" name="save" value="Salvar" />',
					'   <input type="button" name="cancel" value="Cancelar" />',
					'</div>'
				].join('\n'),
			 });

			$("div.contact_info input[type='text']#contact_name").focus();

			$("div.contact_info input[name='save']").on('click', function () {
				var name = $('input#contact_name').val();
				var email = $('input#contact_email').val();
				var addr = $('input#contact_addr').val();

				GLOBALS.last_request = $.ajax({
					url: 'inc/contact_add.php',
					type: 'POST',
					dataType: 'JSON',
					data: {
						name: name,
						email: email,
						address: addr,
						user_email: $("#self_mail").val(),
					}
				}).done(function(data) {
					load_contacts();
					close_modal();
				});

			});

			$("div.contact_info input[name='cancel']").on('click', function () {
				close_modal();
			});

			return false;
		});

		/*
		 * modal para controle geral de pastas
		 */
		$('h2.folder_option').on('click', function () {

			 show_modal({
				title: 'Opções de pasta',
				content: [
				   '<div class="folder_area">',
					'	   <input type="hidden" id="folder_parent" /><br />',
					'	   <div class="add_controler">',
					'		   <a href="javascript:;" class="add_mailbox"><img src="assets/img/btn-add.png" width="auto" height="20" />Nova Pasta</a>',
					'		   <input class="create_area" id="get_name" type="text" />',
					'		   <input class="create_area" id="create_mailbox" type="button" value="Salvar" />',
					'	   </div>',
					'	   <div class="mailboxes_container"></div>',
					'</div>'
				].join('\n'),
			});
			$("div.folder_area input[type='text']#folder_name").focus();
                        
			GLOBALS.last_request = $.ajax({
				url: 'inc/list_mailboxes.php',
				type: 'POST',
				dataType: 'json',
				data: {
					dump_mailboxes: 'read_mailbox_from_file'
				},
				beforeSend: function() {
					toggle_stage_loading();
				}
			}).done(function (data) {
				var tree = data.mailboxes_tree;
				var mailboxes = data.mailboxes;
				var active = get_url_param('box', 'Inbox');

				$('.mailboxes_container').html(
					traverse(tree, 'folder_list', true, true) //true for hidelinks && true for has options
				);

				$('#folder_list').treeview({ collapsed: true });

				toggle_stage_loading();

				$('#folder_list li a').on('click', function() {
					$("#folder_parent").val($(this).data("box"));
					$("#show_folder").text($(this).data("box_name"));
				});

				 //delete
				$('div.folder_area img#folder_del').on('click', function () {
					var $this = $(this).siblings('a');

					var folder = $this.data('box');
					if (!confirm("Excluir pasta " + $this.data('box_name') + "?"))  return;

					GLOBALS.last_request = $.ajax({
						url: 'inc/mailbox_delete.php',
						type: 'POST',
						async: false,
                                                dataType : "json",
						data: {
							boxfull: folder
						},
						beforeSend: function() {
							//toggle_background();
							//toggle_stage_loading();
						}
					}).done(function (data) {
                                                if(data.resp == 'S')
                                                {
                                                    close_modal();
                                                    load_mailboxes({from_server: true});
                                                    toggle_background();
                                                    toggle_stage_loading();
                                                    window.setTimeout('location.reload()', 1000);
                                                }else
                                                {
                                                    alert('A caixa de e-mail não está vazia!');
                                                }

					}).fail(function(){
						show_error();
					});
                                        //<Corrigir>
                                        //window.setTimeout('location.reload()', 1000);
				});

				//rename
				$('div.folder_area img#folder_edit').on('click', function () {
					var $this = $(this);
					var a = $this.siblings('a');

					if (a.find('input').length) {
						a.text(a.data('box_name'));
						return;
					}

					$('div.folder_area img#folder_edit').each(function(){
						var name = $(this).siblings('a').data('box_name');
						$(this).siblings('a').text(name);
					})

					var html = [
						'<input id="rename"	 type="text"/>',
						'<input id="do_rename"   type="button" value="Ok"/>',
					].join("\n");

					a.text("");
					a.html(html);
					$('input#rename').focus();


					$('div.folder_area input#do_rename').on('click', function(){
						var $this = $(this);
						var rename = $this.siblings('input#rename').val();
						var old_name = $this.parents('a').data('box');

						$this.parents('a').text(rename);

						GLOBALS.last_request = $.ajax({
							url: 'inc/update_mailboxe.php',
							type: 'POST',
							data: {
								old_name: old_name,
								rename: rename
							},
							beforeSend: function(){
								close_modal();
								toggle_background();
								toggle_stage_loading()
							}
						}).done(function (data) {
							load_mailboxes({from_server: true});
							toggle_background();
							toggle_stage_loading();
							show_successful();
						});
                                                window.setTimeout('location.reload()', 1000);
					});


				});
                                //<Corrigir>
                                                                

			});
                
			//new folder
			$('div.folder_area a.add_mailbox').on('click', function () {
				$(".create_area").toggle();
			});

			$("div.folder_area input#create_mailbox").on('click', function(){
				var mailbox_name = $("#get_name").val();
				var parent = $("#folder_parent").val();

				if (!mailbox_name) {
					alert("Nomeie a pasta a ser criada");
					return;
				}

				GLOBALS.last_request = $.ajax({
					url: 'inc/mailbox_add.php',
					type: 'POST',
					data: {
						name: mailbox_name,
						parent: parent
					},
					dataType: 'json',
					beforeSend: function() {
						close_modal();
						toggle_background();
						toggle_stage_loading();
					}
                                        }).done(function(){
                                                load_mailboxes({from_server: true});
                                                toggle_stage_loading();
                                                toggle_background();
                                                show_successful();

                                        }).fail(function(){
                                                show_error();

                                        });
                                        //<Corrigir>
                                        window.setTimeout('location.reload()', 1000);
			})

			$('div.folder_area input[name=cancel]').on('click', function () {
				close_modal();
			});

			return false;
		});

		/*
		 * Seta configurações do usuário
		 * user_host_config_.json
		 */

		 $('a.config-button').on('click', function(){
			toggle_stage_loading();

			GLOBALS.last_request = $.ajax({
				url: 'inc/user_config/config.php',
				type: 'POST',
				dataType: 'HTML',
				data: {
					json_config: load_user_config($('#self_mail').val()), //json user config
				},
				beforeSend: function() {
					toggle_stage_loading();
				},
			}).done(function(config_page) {
				if (!config_page) alert("Erro ao carregar configurações");

				show_modal({
					title: 'Preferencias',
					content: config_page,
				});

				CKEDITOR.replace( 'assinatura', {
					toolbar: [
						{ name: 'document', items: [ 'NewPage', 'Preview', '-', 'Templates' ] },
						[ 'Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo' ],
						{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat', '-', 'Image' ] }
					]
				});

				$("div.config_content input[type='button']#save").on('click', function() {
					var $config = $('div.config_content');

					var objConfig = {
						preview_include: $config.find('input#preview_include').prop('checked'),
						preview_redimensionavel: $config.find('input#preview_redimensionavel').prop('checked'),
						production_mode: $config.find('input#production_mode').prop('checked'),
						include_assinatura: $config.find('input#include_assinatura').prop('checked'),
						assinatura: $($('.cke_wysiwyg_frame')[0].contentDocument).find('body').html(), //Ou isso ou instalar um plugins com callback no onChange da instancia do ckeditor. I prefer not. ;)
					}

					var objConfigJSON = JSON.stringify(objConfig);
					var email = $('#self_mail').val();

					GLOBALS.last_request = $.ajax({
						url: 'inc/user_config.php',
						type: 'POST',
						dataType: 'JSON',
						data: {
							config: objConfigJSON,
							email: email,
						},
						beforeSend: function() {
							close_modal();
							toggle_stage_loading();
						},

					}).done(function(jsonConfig) {
						jsonConfigSanitized = sanitizeJSON(jsonConfig);

						set_user_config(jsonConfigSanitized);

						toggle_stage_loading();
						show_successful();

					}).fail(function() {
						toggle_stage_loading();
						show_error();

					});

				});

				$("div.config_content input[type='button']#cancel").on('click', function(){
					close_modal();
				});

			}).fail(function(){
				close_modal();
			});;

		});

		/*
			SEARCH
		*/
		$(".opt-search").on('click',function(){


			var current_mailbox = get_url_param('box');
			show_modal({
				title: 'Busca',
				content: [
					'<div class="search_content left">',
						'<div class="search_options">',
							'<div>',
								'<h3>Buscar por: </h3>',
							'</div>',
							'<div>',
								'<input type="text" name="search">',
								'<img id="do_search" src="assets/img/search.png"/>',
							'</div>',
							'<div>',
								'<div class="left mr_30">',
									'<input type="radio" name="search_criteria" value="subject" checked/>',
									'<label for="assunto">Assunto</label>',
								'</div>',
								'<div class="left mr_30">',
									'<input type="radio" name="search_criteria" value="from"/>',
									'<label for="assunto">Remetente</label>',
								'</div>',
								'<div class="left mr_30">',
									'<input type="radio" name="search_criteria" value="email_text"/>',
									'<label for="assunto">Corpo do email</label>',
								'</div>',
								'<div class="clear"></div>',
							'</div>',
						'</div>',
						 '<!-- <div class="c_more">Mais +</div> -->',

								'<div class="more" style="display: none">',
								'  <div class="left">',
								'	   <div>',
								'		   <input type="radio" name="search_date_criteria" value="before"/>',
								'		   <label for="assunto">Emails antes de:</label>',
								'		   <input id="data_before" type="text" />',
								'		   <input id="data_before_val" type="hidden" />', // RFC2060 '1-jan-2003'
								'	   </div>',
								'	   <div>',
								'		   <input type="radio" name="search_date_criteria" value="since"/>',
								'		   <label for="assunto">Emails desde de:</label>',
								'		   <input id="data_since" type="text" />',
								'		   <input id="data_since_val" type="hidden" />', // RFC2060 '1-jan-2003'
								'	   </div>',
								'  </div>',
								'	 <div class="box_to_search right">',
								'	   <input name="mailbox" type="hidden" value=""/>',
								'	   <div id="list_to_search">',
								'		   <h3>Onde Buscar?</h3>',
								'		   <div class="list_to_search"></div>',
								'	   </div>',
								'   </div>',
								'<div class="clear"></div>',
								'</div>',
						'<div class="search_results">',
							'<table class="search_mail_list" cellspacing="0">',
								'<thead>',
									'<tr class="search_header">',
										'<td><input type="checkbox" class="select_all" /></td>',
                                                                                '<td></td>',
										'<td data-column="subject" class="subject">Assunto</td>',
										'<td data-column="sender" class="sender">Remetente</td>',
										'<td></td>',
										'<td data-column="date" class="date">Data</td>',
									'</tr>',
								'</thead>',
								'<tbody></tbody>',
							'</table>',
						'</div>',
						'<div id="title">',
					'   <a href="javascript:;" class="search_move">Mover Email(s)</a>',
					'</div>',
					'</div>',
					'<div class="search_preview right"></div>',
					'<div class="clear"></div>',
				].join("\n"),
			});

			$.datepicker.regional['pt'] = {
				closeText: 'Fechar',
				prevText: '&#x3c;Anterior',
				nextText: 'Seguinte',
				currentText: 'Hoje',
				monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
				'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
				// monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun', 'Jul','Ago','Set','Out','Nov','Dez'],   -> por causa do RFC
				dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','S&aacute;bado'],
				dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b'],
				dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','S&aacute;b'],
				weekHeader: 'Sem',
				dateFormat: 'dd/mm/yy',
				firstDay: 0,
				isRTL: false,
				showMonthAfterYear: false,
				yearSuffix: ''
			};

			$.datepicker.setDefaults($.datepicker.regional['pt']);

			$('#data_since, #data_before').datepicker("option", "dateFormat", "dd/mm/yy");
			$('#data_since').datepicker({
				altField: "#data_since_val",
				altFormat: "d-M-yy"
			});

			$('#data_before').datepicker({
				altField: "#data_before_val",
				altFormat: "d-M-yy"
			});

			$('input#data_since').on('click', function(){
				$("input[value='since']").click();
				$('input#data_before').val('');
			});

			$('input#data_before').on('click', function(){
				$("input[value='before']").click();
				$('input#data_since').val('');
			});

			// Seta session uids para o evento opt-move.click, que verifica se main_list ou sessionStorage.uids existem e os move
			$(".search_move").on('click', function(){
				var uids = [];
				$('.search input[type=checkbox]:checked').each(function (index, element) {
					var $this = $(element);
					uids.push($this.val());
				});

				if (uids) sessionStorage.setItem('uids', uids);

				$("ul.top-menu a.opt-move")[0].click();
			});

			$('.search_mail_list .select_all').on('click', function(){
				var status = $(this).is(':checked');

				$(".search_mail_list input[type='checkbox']").prop('checked', status);
			});

			 GLOBALS.last_request = $.ajax({
				url: 'inc/list_mailboxes.php',
				type: 'POST',
				dataType: 'json',
				data: {
					dump_mailboxes: 'read_mailbox_from_file'
				},

			}).done(function (data) {
				var tree = data.mailboxes_tree;
				var mailboxes = data.mailboxes;

				$('.list_to_search').html(
					traverse(tree, 'list_to_search', true) //true for hidelinks && null to hide options
				);

				$('#list_to_search').treeview({ collapsed: true });

				$(".list_to_search ul li a").on('click', function(){
					$('#list_to_search li a').css('font-weight', 'normal');
					$(this).css('font-weight', 'bold');
					$("input[name='mailbox']").val($(this).data('box'));
				});

			});

			$('div.search_content div.c_more').on('click', function(){
				$("div.search_content  div.more").toggle();
				$('div.search_content .c_more').text(
					$("div.search_content  div.more").css('display') == 'none'? 'Mais +': 'Menos -'
				);
			});

			$('div.search_content img#do_search').on('click', function() {
				$("div.search_content  div.more").css('display') == 'none'?
							(function(){return;})(): // Só retorna null
							$('div.search_content div.c_more').click();  // Fecha o more/less

				if($('.search_mail_list tbody tr').length) $('.search_mail_list tbody').html("");

				var search_criteria = $("input[name='search_criteria']:checked").val();
				var search = $("div.search_content input[name='search']").val()?
									$("div.search_content input[name='search']").val(): "*";

				var mailbox = $("div.search_content input[name='mailbox']").val()?
									$("div.search_content input[name='mailbox']").val(): get_url_param('box');

				var key = $("input[name='search_date_criteria']:checked").val();
				var val = $("input#data_"+ $("input[name='search_date_criteria']:checked").val() +"_val").val();
				var search_date_criteria = [key, val];

				GLOBALS.last_request = $.ajax({
					url: 'inc/do_search.php',
					data: {
						search_criteria: search_criteria,
						search: search,
						mailbox: mailbox,
						search_date_criteria: search_date_criteria,
					},
					type: 'POST',
					dataType: 'json',
					beforeSend: function () {
						toggle_stage_loading();
					},
				}).done(function (data) {
					toggle_stage_loading();

					var emails = data.emails;

					if (!emails || !emails.length) {
						$('div.search_results table tbody').html('<tr class="no-result"><td colspan="4">Nenhum Resultado Encontrado</td></tr>');
						return false;
					}

					for (var i = 0, odd = 0; i < emails.length; ++i, odd = odd ? 0 : 1) {
						var email = emails[i];
						var tag = [];

						var from_tooltip = email.from;
						var subject_tooltip = '';

						if (email.from != null) email.from = email.from.replace(/\s+&lt;.*$/i, '').replace(/('|"|&quot;)/g, '');

						tag = [
						'<tr class="' + email.li_class + ' mail_search_click" ' + (odd ? 'style="background: #f3f3f3;"' : '') + ' title="Visualizar Email">',
						'	<td class="mail_selector search" style="padding: 1px; width: 25px;"><input type="checkbox" value="' + email.uid + '"></td>',
                                                '       <td></td>',
						'	<td class="mail_subject" title="' + subject_tooltip + '" data-uid="' + email.uid + '"><a href="javascript:;" uid="' + email.uid + '" number="' + email.number + '">' + email.subject + '</a></td>',
						'	<td class="mail_from">' + email.from + '</td>',
						'	<td class="mail_has_att">' + (email.has_att ? '<img src="assets/img/attachment.png"  title="E-mail com anexo" width="20" style="margin-bottom: -4px; margin-right:5px" />' : '') + '</td>',
						'	<td class="mail_date">' + email.date + '</td>',
						'</tr>'
						].join('\n');

						$('div.search_content table.search_mail_list tbody').append($(tag));

					}

					$('.mail_search_click .mail_subject').on('click', function(){
						var email_uid = $(this).data('uid');
						//var mailbox = $("div.search_content input[name='mailbox']").val()?
						//			$("div.search_content input[name='mailbox']").val(): get_url_param('box');
                                                var mailbox = data.mailbox;

						load_email_body(email_uid, mailbox, 'div.search_preview', function(){
							$('div.search_preview').find('.email_link_back').after([
								'<div class="search_toolbar">',
								'   <div class="search_found left" title="Ir para email"></div>',
								'   <div class="close_searched_mail right" title="Buscar mais"></div>',
								'   <div class="clear"></div>',
								'</div>',
							].join("\n"));
							$('div.search_preview').find('.email_link_back').remove();
							$('div.search_preview').find('.email_up').remove();
							$('div.search_preview').find('.email_down').remove();


						   $('.search_found').on('click', function(){
								close_modal();
								GLOBALS.lock_keep_alive = true;
								load_email_body(email_uid, mailbox, 'div.stage');
						   });

						   $('.close_searched_mail').on('click', function(){
							   $('div.search_preview').slideToggle();
							   $('div.search_preview').html("");
						   });

						});

						$('.search_preview').show();

					});

				}).fail(function(){
					show_error();
				});
			});
			//////////////


		});
	});
})(jQuery);

function change_favorite(uid)
{
    if($('#alt_img_favorite_' + uid).attr('src') ==='assets/img/est_01.png')
    {
        $('#alt_img_favorite_' + uid).attr('src', 'assets/img/est_02.png');
        change_favorite_set(uid, 'fav');
    }else
    {
        $('#alt_img_favorite_' + uid).attr('src', 'assets/img/est_01.png');
        change_favorite_set(uid, 'unfav');
    }
}

function change_favorite_set(uid, action)
{
    $.ajax({
        url: 'inc/mail_favorite.php',
        data: {
                uid: uid,
                action: action,
        },
        type: 'POST',
        dataType: 'json'
    });    
}