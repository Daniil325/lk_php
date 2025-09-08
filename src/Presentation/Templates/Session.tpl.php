<div class="sessions_container">
    <h2>Мои сессии</h2>

    <div class="items">
        <?php foreach ($data as $session): ?>
            <div class="session_item">
                <div class="session_details">
                    <div class="detail_item">
                        <svg class="detail_icon icon-session" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        <div class="detail_content">
                            <div class="detail_label">ID сессии</div>
                            <div class="detail_value">
                                <span class="session_id"><?= htmlspecialchars($session->id) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="detail_item">
                        <svg class="detail_icon icon-login" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1" />
                        </svg>
                        <div class="detail_content">
                            <div class="detail_label">Дата входа</div>
                            <div class="detail_value date_value"><?= htmlspecialchars($session->data->dateIn) ?></div>
                        </div>
                    </div>

                    <div class="detail_item">
                        <svg class="detail_icon icon-email" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <div class="detail_content">
                            <div class="detail_label">Email</div>
                            <div class="detail_value email_value"><?= htmlspecialchars($session->data->userData['email']) ?></div>
                        </div>
                    </div>

                    <div class="detail_item">
                        <svg class="detail_icon icon-time" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="detail_content">
                            <div class="detail_label">Последнее изменение файла</div>
                            <div class="detail_value date_value"><?= date('Y-m-d H:i:s', $session->timestamp) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>