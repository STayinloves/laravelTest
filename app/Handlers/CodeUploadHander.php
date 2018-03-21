<?php

namespace App\Handlers;

class CodeUploadHandler
{

    public function save($file, $username, $pid, $lang)
    {

        $upload_path = public_path() . '/uploads/code/judging';

        $ext = ['', 'c', 'cpp', 'java', 'py', 'py', 'cs', 'rb', 'pas'];
        $langs = ['', 'c', 'c++', 'java', 'python2', 'python3', 'c#', 'ruby', 'pascal'];

        $extension = $ext[$lang];

        $filename = $username . '_' . $pid . '_' . time() . '_' . $langs[$lang] . '.' . $extension;
        $file->move($upload_path, $filename);
        return [
            'path' => config('app.url') . "/$filename"
        ];
    }
}