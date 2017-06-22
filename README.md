## Lexicon v2.0.0 (EE3)

Low Variables compatible fieldtype for managing translated words and phrases. Optionally display text inputs or textareas.

You'll need to edit the following files to determine your set of custom langauges:

### `ft.lexicon.php`

```php
protected $fields = array(
    'lang_en'       => '',
    'lang_pl'       => '',
    'lang_ru'       => '',
    'lang_tr'       => '',
);
```


### `language/english/lang.lexicon.php`

$lang = array(

    'lexicon_lang_en'       => 'English (en)',
    'lexicon_lang_pl'       => 'Polski (pl)',
    'lexicon_lang_ru'       => 'Pусский (ru)',
    'lexicon_lang_tr'       => 'Türk (tr)',

    ''=>''
);