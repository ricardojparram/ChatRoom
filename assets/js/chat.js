$(document).ready(function () {
	$('main').animate({ opacity: 'toggle' }, 2000);

	// ── Obtener usuario de sesión ───────────────────────────────────────────
	let user;
	$.post('', { username: '' }, function (response) {
		user = JSON.parse(response);
	});

	// ── Helpers ─────────────────────────────────────────────────────────────
	function scrollBottom() {
		let box = $('.chat-box')[0];
		box.scrollTop = box.scrollHeight;
	}

	function escapeHtml(text) {
		return $('<span>').text(String(text)).html();
	}

	function formatTime(isoStr) {
		if (!isoStr) return '';
		let d = new Date(isoStr.replace(' ', 'T') + (isoStr.includes('T') ? '' : 'Z'));
		let pad = n => String(n).padStart(2, '0');
		return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
	}

	// ── Renderizar mensaje de chat general ───────────────────────────────────
	function renderMessage(id, username, message, isMe, edited, createdAt, deleted) {
		let idAttr = id ? `data-id="${id}"` : '';
		let time = createdAt ? `<span class="msg-time">${escapeHtml(formatTime(createdAt))}</span>` : '';

		let html;
		if (deleted) {
			let deletedClass = isMe ? 'msg me deleted' : 'msg deleted';
			html = `
				<div class="${deletedClass}" ${idAttr}>
					<p class="msg-deleted-text">🗑 Deleted message</p>
					<div class="msg-footer">${time}</div>
				</div>`;
		} else if (isMe) {
			let editTag = edited ? '<span class="msg-edited">(edited)</span>' : '';
			let actions = id ? `
				<span class="msg-actions">
					<button class="edit-btn"  title="Edit">✎</button>
					<button class="delete-btn" title="Delete">🗑</button>
				</span>` : '';
			html = `
				<div class="msg me" ${idAttr}>
					<p class="msg-text">${escapeHtml(message)}</p>
					<div class="msg-footer">
						${editTag}
						${time}
						${actions}
					</div>
				</div>`;
		} else {
			html = `
				<div class="msg" ${idAttr}>
					<p class="username">${escapeHtml(username)}</p>
					<p class="msg-text">${escapeHtml(message)}</p>
					<div class="msg-footer">${time}</div>
				</div>`;
		}
		$('.chat-box').append(html);
	}

	// ── Edición inline ───────────────────────────────────────────────────────
	$(document).on('click', '.edit-btn', function () {
		let msgDiv = $(this).closest('.msg');
		let msgId = msgDiv.data('id');
		let textEl = msgDiv.find('.msg-text');
		let current = textEl.text();

		if (msgDiv.find('.edit-input').length) return;

		textEl.hide();
		msgDiv.find('.msg-footer').hide();

		let editRow = $(`
			<div class="edit-row">
				<input class="edit-input" type="text" value="${escapeHtml(current)}">
				<button class="edit-save">✓</button>
				<button class="edit-cancel">✕</button>
			</div>
		`);
		msgDiv.append(editRow);
		editRow.find('.edit-input').focus().select();

		function cancelEdit() {
			editRow.remove();
			textEl.show();
			msgDiv.find('.msg-footer').show();
		}

		function saveEdit() {
			let newText = editRow.find('.edit-input').val().trim();
			if (!newText || newText === current) { cancelEdit(); return; }

			$.ajax({
				url: '?module=message',
				method: 'PUT',
				contentType: 'application/json',
				data: JSON.stringify({ id: msgId, message: newText }),
				success: function () {
					textEl.text(newText).show();
					editRow.remove();
					msgDiv.find('.msg-footer').show();
					if (!msgDiv.find('.msg-edited').length) {
						msgDiv.find('.msg-footer').prepend('<span class="msg-edited">(edited)</span>');
					}
				},
				error: function () { cancelEdit(); alert('Could not edit message.'); }
			});
		}

		editRow.find('.edit-save').click(saveEdit);
		editRow.find('.edit-cancel').click(cancelEdit);
		editRow.find('.edit-input').on('keydown', function (e) {
			if (e.key === 'Enter') { e.preventDefault(); saveEdit(); }
			if (e.key === 'Escape') { cancelEdit(); }
		});
	});

	// ── Eliminación ─────────────────────────────────────────────────────────
	$(document).on('click', '.delete-btn', function () {
		let msgDiv = $(this).closest('.msg');
		let msgId = msgDiv.data('id');
		if (!msgId) return;
		if (!confirm('Delete this message?')) return;

		$.ajax({
			url: '?module=message',
			method: 'DELETE',
			contentType: 'application/json',
			data: JSON.stringify({ id: msgId }),
			success: function () {
				msgDiv.addClass('deleted');
				msgDiv.find('.msg-text').remove();
				msgDiv.find('.msg-footer').html(
					`<p class="msg-deleted-text">🗑 Deleted message</p>` + msgDiv.find('.msg-footer').html()
				);
				msgDiv.find('.msg-footer .msg-edited, .msg-footer .msg-actions').remove();
			},
			error: function () { alert('Could not delete message.'); }
		});
	});

	// ── Cargar historial desde la BD ────────────────────────────────────────
	function loadHistory() {
		$.get('?module=message', function (data) {
			let messages = typeof data === 'string' ? JSON.parse(data) : data;
			messages.forEach(function (m) {
				renderMessage(
					m.id, m.username, m.message,
					m.username === user,
					!!m.edited, m.created_at, !!m.deleted
				);
			});
			scrollBottom();
		});
	}

	// ── Guardar mensaje ──────────────────────────────────────────────────────
	function saveMessage(message) {
		return $.ajax({
			url: '?module=message',
			method: 'POST',
			contentType: 'application/json',
			data: JSON.stringify({ message: message })
		});
	}

	// ── WebSocket ────────────────────────────────────────────────────────────
	let conex = new WebSocket(`ws://${socket_front}`);

	conex.onopen = function () {
		let waitUser = setInterval(function () {
			if (user !== undefined) {
				clearInterval(waitUser);
				// Anunciarse al servidor con el username
				conex.send(JSON.stringify({ type: 'join', username: user }));
				loadHistory();
			}
		}, 50);
	};

	conex.onmessage = function (e) {
		let response = JSON.parse(e.data);

		// Lista de usuarios conectados
		if (response.type === 'user_list') {
			renderUserList(response.users);
			return;
		}

		// DM recibido
		if (response.type === 'dm') {
			openDMPanel(response.from);
			appendDMMessage(response.from, response.from, response.message, false);
			flashDMTab(response.from);
			return;
		}

		// Confirmación de DM enviado
		if (response.type === 'dm_sent') {
			appendDMMessage(response.to, user, response.message, true);
			return;
		}

		// Error de DM
		if (response.type === 'dm_error') {
			showDMError(response.error);
			return;
		}

		// Mensaje normal de chat
		renderMessage(null, response.username, response.message, false, false, new Date().toISOString(), false);
		scrollBottom();
	};

	// ── Lista de usuarios online ─────────────────────────────────────────────
	function renderUserList(users) {
		let list = $('#online-users');
		list.empty();
		users.forEach(function (u) {
			if (u === user) {
				// Yo mismo — sin link DM
				list.append(`<li class="online-user-item me"><span class="online-dot"></span>${escapeHtml(u)} <em>(you)</em></li>`);
			} else {
				list.append(`<li class="online-user-item" data-username="${escapeHtml(u)}"><span class="online-dot"></span>${escapeHtml(u)}</li>`);
			}
		});
		$('#online-count').text(users.length);
	}

	// Clic en usuario → abrir DM
	$(document).on('click', '.online-user-item[data-username]', function () {
		let target = $(this).data('username');
		openDMPanel(target);
	});

	// ── DM Panel ─────────────────────────────────────────────────────────────
	// dmPanels = { username: { messages: [] } }
	let dmPanels = {};
	let activeDM = null;

	function openDMPanel(withUser) {
		let overlay = $('#dm-overlay');
		overlay.removeClass('hidden');

		// Crear tab si no existe
		if (!dmPanels[withUser]) {
			dmPanels[withUser] = { messages: [] };
			addDMTab(withUser);
			addDMConversation(withUser);
		}
		switchDMTab(withUser);
	}

	function addDMTab(withUser) {
		let tab = $(`<button class="dm-tab" data-dm="${escapeHtml(withUser)}">${escapeHtml(withUser)}<span class="dm-tab-close" data-dm="${escapeHtml(withUser)}">✕</span></button>`);
		$('#dm-tabs').append(tab);
	}

	function addDMConversation(withUser) {
		let conv = $(`
			<div class="dm-conversation hidden" data-dm="${escapeHtml(withUser)}">
				<div class="dm-box"></div>
				<div class="dm-input-row">
					<textarea class="dm-textarea" placeholder="Message ${escapeHtml(withUser)}..."></textarea>
					<button class="dm-send-btn" data-dm="${escapeHtml(withUser)}"><span class="material-icons">send</span></button>
				</div>
			</div>
		`);
		$('#dm-conversations').append(conv);
	}

	function switchDMTab(withUser) {
		activeDM = withUser;
		// Tabs
		$('.dm-tab').removeClass('active');
		$(`.dm-tab[data-dm="${withUser}"]`).addClass('active').removeClass('unread');
		// Conversaciones
		$('.dm-conversation').addClass('hidden');
		$(`.dm-conversation[data-dm="${withUser}"]`).removeClass('hidden');
		// Focus textarea
		$(`.dm-conversation[data-dm="${withUser}"] .dm-textarea`).focus();
	}

	function appendDMMessage(withUser, fromUser, message, isMe) {
		let box = $(`.dm-conversation[data-dm="${withUser}"] .dm-box`);
		let now = formatTime(new Date().toISOString());
		let cls = isMe ? 'dm-msg dm-me' : 'dm-msg';
		let nameHtml = isMe ? '' : `<p class="dm-username">${escapeHtml(fromUser)}</p>`;
		box.append(`
			<div class="${cls}">
				${nameHtml}
				<p class="dm-text">${escapeHtml(message)}</p>
				<span class="dm-time">${now}</span>
			</div>
		`);
		// Scroll al fondo
		let boxEl = box[0];
		boxEl.scrollTop = boxEl.scrollHeight;
	}

	function flashDMTab(withUser) {
		if (activeDM !== withUser) {
			$(`.dm-tab[data-dm="${withUser}"]`).addClass('unread');
		}
	}

	function showDMError(msg) {
		let box = $(`.dm-conversation[data-dm="${activeDM}"] .dm-box`);
		box.append(`<div class="dm-error">⚠ ${escapeHtml(msg)}</div>`);
	}

	// ── Cerrar DM overlay ────────────────────────────────────────────────────
	$('#dm-close').on('click', function () {
		$('#dm-overlay').addClass('hidden');
		activeDM = null;
	});

	// ── Cerrar tab individual ────────────────────────────────────────────────
	$(document).on('click', '.dm-tab-close', function (e) {
		e.stopPropagation();
		let target = $(this).data('dm');
		$(`.dm-tab[data-dm="${target}"]`).remove();
		$(`.dm-conversation[data-dm="${target}"]`).remove();
		delete dmPanels[target];

		// Si no quedan tabs, cerrar overlay
		if ($('.dm-tab').length === 0) {
			$('#dm-overlay').addClass('hidden');
			activeDM = null;
		} else {
			// Activar primer tab restante
			let firstTab = $('.dm-tab').first().data('dm');
			switchDMTab(firstTab);
		}
	});

	// ── Click en tab → cambiar conversación ──────────────────────────────────
	$(document).on('click', '.dm-tab', function () {
		let target = $(this).data('dm');
		if (target) switchDMTab(target);
	});

	// ── Enviar DM ────────────────────────────────────────────────────────────
	function sendDM(toUser) {
		let textarea = $(`.dm-conversation[data-dm="${toUser}"] .dm-textarea`);
		let msg = textarea.val().trim();
		if (!msg) return;
		textarea.val('');
		if (conex.readyState === WebSocket.OPEN) {
			conex.send(JSON.stringify({ type: 'dm', to: toUser, message: msg }));
		}
	}

	$(document).on('click', '.dm-send-btn', function () {
		let toUser = $(this).data('dm');
		sendDM(toUser);
	});

	$(document).on('keydown', '.dm-textarea', function (e) {
		if (e.key === 'Enter' && !e.shiftKey) {
			e.preventDefault();
			let toUser = $(this).closest('.dm-conversation').data('dm');
			sendDM(toUser);
		}
	});

	// ── Enviar mensaje general ───────────────────────────────────────────────
	function sendMessage() {
		let textarea = $('#msg');
		let msg = textarea.val().trim();
		if (!msg) return;
		textarea.val('');

		let now = new Date().toISOString();

		saveMessage(msg).done(function (res) {
			let data = typeof res === 'string' ? JSON.parse(res) : res;
			let newId = data.id || null;
			renderMessage(newId, user, msg, true, false, now, false);
			scrollBottom();
			if (conex.readyState === WebSocket.OPEN) {
				conex.send(JSON.stringify({ username: user, message: msg }));
			}
		}).fail(function () {
			renderMessage(null, user, msg, true, false, now, false);
			scrollBottom();
			if (conex.readyState === WebSocket.OPEN) {
				conex.send(JSON.stringify({ username: user, message: msg }));
			}
		});
	}

	$('#msg').on('keydown', function (e) {
		if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
	});
	$('#sendMessage').click(function (e) { e.preventDefault(); sendMessage(); });
	$('#logout').click(() => { window.location = '?module=logout'; });
});