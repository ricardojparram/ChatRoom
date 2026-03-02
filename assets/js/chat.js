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

	// ── Renderizar mensaje ───────────────────────────────────────────────────
	function renderMessage(id, username, message, isMe, edited, createdAt, deleted) {
		let idAttr = id ? `data-id="${id}"` : '';
		let time = createdAt ? `<span class="msg-time">${escapeHtml(formatTime(createdAt))}</span>` : '';

		let html;
		if (deleted) {
			// Soft-deleted: mostrar placeholder, sin botones
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
				// Convertir in-place a "Deleted message"
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
				loadHistory();
			}
		}, 50);
	};

	conex.onmessage = function (e) {
		let response = JSON.parse(e.data);
		renderMessage(null, response.username, response.message, false, false, new Date().toISOString(), false);
		scrollBottom();
	};

	// ── Enviar ───────────────────────────────────────────────────────────────
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