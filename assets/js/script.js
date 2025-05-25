$(document).ready(function () {
    // LOGIN FORM
    $('#loginForm').submit(function (e) {
        e.preventDefault();

        $.post('core/handleForms.php', {
            action: 'login',
            email: $('#email').val(),
            password: $('#password').val()
        }, function (response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                window.location.reload();
            } else {
                $('#loginMessage').text(res.message);
            }
        });
    });

    // REGISTER FORM
    $('#registerForm').submit(function (e) {
        e.preventDefault();

        $.post('core/handleForms.php', {
            action: 'register',
            name: $('#name').val(),
            email: $('#email').val(),
            password: $('#password').val()
        }, function (response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert("Registration successful! You can now log in.");
                window.location.href = "index.php";
            } else {
                $('#registerMessage').text(res.message || "Registration failed.");
            }
        });
    });

    // AUTO-SAVE EDITOR (USED IN edit_doc.php)
    let autoSaveTimer;
    $('#editor').on('input', function () {
        clearTimeout(autoSaveTimer);

        autoSaveTimer = setTimeout(() => {
            const content = $('#editor').html();
            const docId = $('#docId').val();

            $.post('../core/handleForms.php', {
                action: 'autosave',
                doc_id: docId,
                content: content
            }, function (res) {
                const result = JSON.parse(res);
                if (result.status === 'success') {
                    $('#status').text('Auto-saved at ' + new Date().toLocaleTimeString());
                } else {
                    $('#status').text('Save failed');
                }
            });
        }, 1000); // Save after 1 second of inactivity
    });

    // SUSPEND/UNSUSPEND USERS (USED IN manage_users.php)
    $('.suspend-toggle').on('change', function () {
        const userId = $(this).data('id');
        const suspend = $(this).is(':checked') ? 1 : 0;

        $.post('../core/handleForms.php', {
            action: 'toggle_suspend',
            user_id: userId,
            suspend: suspend
        }, function (response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert('User status updated.');
            } else {
                alert('Failed to update user.');
            }
        });
    });

    // SHARE DOCUMENT LIVE SEARCH (USED IN share_doc.php)
    $('#userSearch').on('input', function () {
        const term = $(this).val();
        if (term.length < 2) {
            $('#searchResults').html('');
            return;
        }

        $.get('../core/handleForms.php', {
            action: 'search_user',
            term: term
        }, function (response) {
            const users = JSON.parse(response);
            let html = '';
            users.forEach(user => {
                html += `<div class="user-item" data-id="${user.id}">${user.name} (${user.email})</div>`;
            });
            $('#searchResults').html(html);
        });
    });

    // SHARE DOCUMENT CLICK
    $(document).on('click', '.user-item', function () {
        const userId = $(this).data('id');
        const docId = $('#docId').val();

        $.post('../core/handleForms.php', {
            action: 'share_user',
            doc_id: docId,
            user_id: userId
        }, function (response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert('User added to document.');
                location.reload();
            } else {
                alert('Failed to share document.');
            }
        });
    });

    // MESSAGE SEND (USED IN messages.php)
    $('#chatForm').submit(function (e) {
        e.preventDefault();

        const message = $('#message').val();
        const docId = $('#docId').val();

        if (message.trim() === '') return;

        $.post('../core/handleForms.php', {
            action: 'send_message',
            doc_id: docId,
            message: message
        }, function (res) {
            const result = JSON.parse(res);
            if (result.status === 'success') {
                location.reload(); // Or you can append without reloading
            } else {
                alert("Failed to send message.");
            }
        });
    });
});
