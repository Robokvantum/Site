<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе
 * установки. Необязательно использовать веб-интерфейс, можно
 * скопировать файл в "wp-config.php" и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки MySQL
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Параметры MySQL: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'wprobokvantum' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'wprobokvantum' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', 'sdjvbDFH234&dkfj' );

/** Имя сервера MySQL */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу.
 * Можно сгенерировать их с помощью {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными. Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'waG4hV6Gl8jTxGG.(p:=<d`NB-+5NraA0#P|}5*}Bm9ThItfDbHfW<7{|(YC]0s)' );
define( 'SECURE_AUTH_KEY',  '<Rzk7%v?wCd 6x-d]7l|Rdmf/G@^!OKX%&tuf`[ytsL/O+N=AWlc%>EBnFA)RTg4' );
define( 'LOGGED_IN_KEY',    '2v*`/I3G)eSa374SB:U6y&svjX+YViRd;+#~>4Y[z4)gE;12:Lp1=Y|oeqx7qJ?}' );
define( 'NONCE_KEY',        '8n1i/F}6u~*^kDR1Q_dlH]JoJe&C4Y9Hf6g3Z^tySFk]2Hk2p_fJGV#ihLEsk0uu' );
define( 'AUTH_SALT',        'P/fo*zrBi0])lpBQg:;uI#!&5YA`%HYz;hv&ECT>wd{oA?011wqc.+&C.c(rsPoO' );
define( 'SECURE_AUTH_SALT', '}(wa_UEpnK,NR)=]K7ZupW?[Gx5+bA?}^G_zWL(AWD9mg?s!yTR={D=srkm~Jm|L' );
define( 'LOGGED_IN_SALT',   'IYU,_t(E*.Q#B9hwyrevV{:PDp8wjU}%%A$ql>;UP`5/:-Y0q^3KLcf~%)O~s3#,' );
define( 'NONCE_SALT',       'g}SK|F_tf`S09#9g&]U*8><3fgl</;R=8Xuo `W/1T3[_R| Z,6w,kF@/!~fs00f' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wpr_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в Кодексе.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once( ABSPATH . 'wp-settings.php' );
