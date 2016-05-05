<?php

$html = <<<HTML
<html>
<head>
    <title>Test form</title>
</head>
    <body>
        <form enctype="multipart/form-data" method="POST" action="/upload/">
            <input name="report" type="file">
            <input name="description" type="hidden" value="I'm hidden filed"/>
            <input name="submit" type="submit"/>
        </form>
    </body>
</html>
HTML;

echo $html;
