<?php
session_start();
session_destroy();
header('Location: /sc502-ln-proyecto-grupo5-ln-2026/Index.php');
exit;