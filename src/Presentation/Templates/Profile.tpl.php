<div class="profile_container">

    <div class="user_card">
        <div class="user_avatar_container">
            <div class="avatar_placeholder">
                <img width="300" src="data:image/*;base64,<?php echo $data->photoData ?>" alt="">
            </div>
        </div>

        <div class="user_info">
            <div class="info_item">
                <svg class="info_icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-9 4a1 1 0 011-1h8a1 1 0 011 1v3a1 1 0 01-1 1H8a1 1 0 01-1-1v-3z" />
                </svg>
                <span class="info_label">Имя фамилия:</span>
                <span class="info_value"><?php echo htmlspecialchars($data->surname); ?> <?php echo htmlspecialchars($data->name); ?></span>
            </div>

            <div class="info_item">
                <svg class="info_icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 012-2h4a2 2 0 012 2v1m-6 0h8m-9 4a1 1 0 011-1h8a1 1 0 011 1v3a1 1 0 01-1 1H8a1 1 0 01-1-1v-3z" />
                </svg>
                <span class="info_label">Возраст:</span>
                <span class="info_value"><?php echo htmlspecialchars($data->age); ?></span>
            </div>

            <div class="info_item">
                <svg class="info_icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="info_label">Почта:</span>
                <span class="info_value"><?php echo htmlspecialchars($data->email); ?></span>
            </div>

            <div class="info_item">
                <svg class="info_icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="info_label">Пол:</span>
                <span class="info_value"><?php echo htmlspecialchars($data->sex); ?></span>
            </div>
        </div>
    </div>
</div>