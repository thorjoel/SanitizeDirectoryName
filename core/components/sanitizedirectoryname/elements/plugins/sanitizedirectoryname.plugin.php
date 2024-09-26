<?php

/*
*
*   Event: OnFileManagerDirCreate
*   Author: Henning H. Haugen
*   Adapter: Thor JB Tømmerås
*   Requirements: MODX 3 and above
*
*/

if ($modx->event->name != 'OnFileManagerDirCreate') {
    return;
}

// Finding the path of the directory created
$old_path = $modx->event->params['directory'];

// Make sure to use an absolute path
$absolute_old_path = MODX_BASE_PATH . $old_path;

// Check if the absolute path exists
if (!file_exists($absolute_old_path) || !is_dir($absolute_old_path)) {
    $modx->log(modX::LOG_LEVEL_ERROR, "Original path does not exist: $absolute_old_path");
    return;
}

// Exploding the path to get the name of the directory
$split = explode('/', $absolute_old_path);

// Finding the name of the directory by getting the last element of the array and removing it
$name = array_pop($split);

// Using filterPathSegment to URL-encode the directory name
$newname = $modx->filterPathSegment($name, [
    'friendly_alias_restrict_chars_pattern' => $modx->getOption('friendly_alias_restrict_chars_pattern')
]);

// Inserting the new directory name at the end of the array
$split[] = $newname;

// Imploding the exploded array to create the full path
$absolute_new_path = implode('/', $split);

// Log the new path
$modx->log(modX::LOG_LEVEL_ERROR, "Attempting to rename $absolute_old_path to $absolute_new_path");

// Check if the new path already exists
if (file_exists($absolute_new_path)) {
    $modx->log(modX::LOG_LEVEL_ERROR, "A directory with the cleaned name already exists: $absolute_new_path. Renaming is not allowed.");
    
    // Optionally, remove the originally created directory if needed
    if (is_dir($absolute_old_path)) {
        rmdir($absolute_old_path);
        $modx->log(modX::LOG_LEVEL_ERROR, "Removed the originally created directory: $absolute_old_path");
    }
    return;
}

// Attempt to rename the directory with error handling
try {
    if (rename($absolute_old_path, $absolute_new_path)) {
        // Log success message
        // $modx->log(modX::LOG_LEVEL_INFO, "Directory successfully renamed from $absolute_old_path to $absolute_new_path");
    } else {
        // Capture the last error in case rename fails
        $error = error_get_last();
        $modx->log(modX::LOG_LEVEL_ERROR, "Failed to rename directory from $absolute_old_path to $absolute_new_path: " . $error['message']);
    }
} catch (Exception $e) {
    $modx->log(modX::LOG_LEVEL_ERROR, "Exception while renaming: " . $e->getMessage());
}
