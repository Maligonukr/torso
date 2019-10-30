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
define( 'DB_NAME', 'torso' );

/** Имя пользователя MySQL */
define( 'DB_USER', 'root' );

/** Пароль к базе данных MySQL */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'n):>t(wAHv%*n(rLB=sEd&JT~dN~U_( [<yWq>E7,QcjUhY1@lk0DZ{8{YCm*Tg8' );
define( 'SECURE_AUTH_KEY',  '7j^@X4l{`Nkwr__UcG@%rOn4Fvb }-1#mZ<$3~#t|uS3pvZI$4P:M!52Vp8*j_:{' );
define( 'LOGGED_IN_KEY',    'ss}/hc@ 50#G82oJmo`#OHpein]`)e1I4rT0m=B &8)qwbzF}EN*tY^~DSB||;C?' );
define( 'NONCE_KEY',        'D&}Z.CTos*8S`kkx2&q(B(N;5Z`P/ Yq{=]icB*<0#lPk4]RBSc#|VT9|/b[nc`]' );
define( 'AUTH_SALT',        'a2,sQah3jYB&HD{T41bP?%<rnbgG&iJ:F9Aa_cD94A]s5tv]H!bEZ*;qbHzI!rS<' );
define( 'SECURE_AUTH_SALT', 'FQ)AA+pP`G:r}(:E:$|$/j(uEMS7r:i*z=Wf5j!O/Hi)eGLTB/b&jU{N%J+_7UwV' );
define( 'LOGGED_IN_SALT',   '#Z)6hOR-Z8K!2k=6s,r}sT0hBOz.l~oO`tqT[7Lgh62vc:,i<V44FnQE&+k<%1PI' );
define( 'NONCE_SALT',       'GCN6x`$B#z^l)pN~n!:.5d@&sJ#{0Y=`F_]U{Uou>~>ZiUl2i?eq{[$.52o]KRs#' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

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
