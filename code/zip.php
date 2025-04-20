<?php
// 디렉토리 압축 함수
function zipDirectory($source, $destination) {
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZipArchive::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            $file = str_replace('\\', '/', $file);

            // 무시할 항목 건너뛰기
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                continue;
            }

            $file = realpath($file);

            if (is_dir($file) === true) {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            } elseif (is_file($file) === true) {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    } elseif (is_file($source) === true) {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

// 압축할 디렉토리와 저장할 파일 경로 설정
$source = '/home/db2024/2022320301/public_html/termproject'; // 압축할 디렉토리 경로
$destination = 'termproject.zip'; // 저장할 zip 파일 경로

// 디렉토리 압축 실행
if (zipDirectory($source, $destination)) {
    echo '압축이 성공적으로 완료되었습니다.';
} else {
    echo '압축에 실패했습니다.';
}
?>
