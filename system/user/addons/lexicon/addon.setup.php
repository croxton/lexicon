<?php

if (! defined('LEXICON_AUTHOR')) {
	define('LEXICON_AUTHOR', 'Mark Croxton, Hallmark Design');
	define('LEXICON_AUTHOR_URL', 'http://hallmark-design.co.uk');
	define('LEXICON_DESC', 'Fieldtype for managing translated words and phrases.');
	define('LEXICON_DOCS_URL', 'https://github.com/croxton/lexicon');
	define('LEXICON_NAME', 'Lexicon');
	define('LEXICON_VER', '2.0.0');
}

return array(
	'author' => LEXICON_AUTHOR,
	'author_url' => LEXICON_AUTHOR_URL,
	'description' => LEXICON_DESC,
	'docs_url' => LEXICON_DOCS_URL,
	'name' => LEXICON_NAME,
	'namespace' => 'Croxton\Lexicon',
	'version' => LEXICON_VER
);