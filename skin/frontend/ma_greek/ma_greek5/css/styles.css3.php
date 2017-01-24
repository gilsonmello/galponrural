<?php
    header('Content-type: text/css; charset: UTF-8');
    header('Cache-Control: must-revalidate');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 3600) . ' GMT');
    $url = $_REQUEST['url'];
?>

@-webkit-keyframes moveFromTop {
    from {
        -webkit-transform: translateY(-100%);
    }
    to {
        -webkit-transform: translateY(0%);
    }
}
@-moz-keyframes moveFromTop {
    from {
        -moz-transform: translateY(-100%);
    }
    to {
        -moz-transform: translateY(0%);
    }
}
@-ms-keyframes moveFromTop {
    from {
        -ms-transform: translateY(-100%);
    }
    to {
        -ms-transform: translateY(0%);
    }
}

@-webkit-keyframes smallToBig{
    from {
        -webkit-transform: scale(0.1);
    }
    to {
        -webkit-transform: scale(1);
    }
}
@-moz-keyframes smallToBig{
    from {
        -moz-transform: scale(0.1);
    }
    to {
        -moz-transform: scale(1);
    }
}
@-ms-keyframes smallToBig{
    from {
        -ms-transform: scale(0.1);
    }
    to {
        -ms-transform: scale(1);
    }
}

@keyframes rotate{
    0% { transform: scale(1) rotate(0);}
    50% { transform: scale(0.5) rotate(180deg);}
    100% { transform: scale(1) rotate(360deg);}
}
@-webkit-keyframes rotate{
    0% { -webkit-transform: scale(1) rotate(0);}
    50% { -webkit-transform: scale(0.5) rotate(180deg);}
    100% { -webkit-transform: scale(1) rotate(360deg);}
}
@-moz-keyframes rotate{
    0% { -moz-transform: scale(1) rotate(0);}
    50% { -moz-transform: scale(0.5) rotate(180deg);}
    100% { -moz-transform: scale(1) rotate(360deg);}
}

ul.link-follow li a span
{
-ms-filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=0)";
filter: alpha(opacity=0);
opacity: 0;	
}

ul.link-follow li a:hover span
{
-ms-filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=100)";
filter: alpha(opacity=100);
opacity: 100;
}

{
-moz-border-radius:4px;
-webkit-border-radius:4px;
border-radius:4px;
}


{
-moz-border-radius:5px;
-webkit-border-radius:5px;
border-radius:5px;
}

{
transition: 0.3s ease-in-out;
-moz-transition: 0.3s ease-in-out;
-webkit-transition: 0.3s ease-in-out;
-o-transition: 0.3s ease-in-out;
-ms-transition: 0.3s ease-in-out
}

{
-ms-filter: "progid: DXImageTransform.Microsoft.Alpha(Opacity=80)";
filter: alpha(opacity=80);
opacity: 0.8;
}




{
-webkit-transition: all 300ms linear;
-moz-transition: all 300ms linear;
-o-transition: all 300ms linear;
-ms-transition: all 300ms linear;
transition: all 300ms linear;
}

{
transition: 0.4s ease-in-out;
-moz-transition: 0.4s ease-in-out;
-webkit-transition: 0.4s ease-in-out;
-o-transition: 0.4s ease-in-out;
-ms-transition: 0.4s ease-in-out    
}

{
-webkit-transition: background 0.3s, color 0.2s;
-moz-transition: background 0.3s, color 0.2s;
transition: background 0.3s, color 0.2s;
}

.ma-box-content .actions
{
-webkit-transition: all 0.5s cubic-bezier(0.215, 0.61, 0.355, 1);
-moz-transition: all 0.5s cubic-bezier(0.215, 0.61, 0.355, 1);
-o-transition: all 0.5s cubic-bezier(0.215, 0.61, 0.355, 1);
transition: all 0.5s cubic-bezier(0.215, 0.61, 0.355, 1);    
}