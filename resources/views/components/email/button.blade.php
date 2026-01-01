<div style="margin-bottom: 20px;text-align:center">
    <!--[if mso]>
    <v:roundrect
        xmlns:v="urn:schemas-microsoft-com:vml"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        href="{{ $url }}"
        style="height:42px;v-text-anchor:middle;width:auto;min-width:220px;padding-left:16px;padding-right:16px;"
        arcsize="15%"
        strokecolor="#E97D32"
        fillcolor="#E97D32"
    >
        <w:anchorlock/>
        <center
            style="
                color: #ffffff;
                font-family: 'Poppins', sans-serif;
                font-size: 18px;
                font-weight: bold;
            "
        >
            {{ $text }}
        </center>
        </v:roundrect>
    <![endif]-->
    <a href="{{ $url }}"
        style="background-color:#E97D32; border:1px solid #E97D32;border-radius:6px;color:#ffffff;display:inline-block;font-family: 'Poppins', sans-serif;font-size:18px;font-weight:bold;line-height:42px;text-decoration:none;width:auto;min-width:220px;padding-left:16px;padding-right:16px;-webkit-text-size-adjust:none;mso-hide:all;text-align:center;">
        {{ $text }}
    </a>
</div>
