<?php
if ($modx->event->name != 'OnFileManagerDirCreate') {return;}

//Finding the path of the directory created
$old_path = $modx->event->params['directory'];

//Exploding the path to get the name of the directory.
$split = explode('/', $old_path);

//Finding the name of the directory by getting the last element of the array and removing it
$name = array_pop($split);

//Using filterPathSegment to URL-encode the directory name.
$newname = $modx->filterPathSegment($name, ['friendly_alias_restrict_chars_pattern' => $modx->getOption('friendly_alias_restrict_chars_pattern')]);

//Inserting the new directory name at the end of the array
$split[] = $newname;

//Imploding the exploded array to create the full path
$new_path = implode('/', $split);

//Renaming the full path
rename($old_path, $new_path);
