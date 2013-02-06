<?php

    require_once 'boot/const.php';

    #
    # Remove all line feeds from the string
    #
    function eliminar_saltos_linea($texto)
    {
        $cleaned_html = str_replace("\r", "", $texto);
        $cleaned_html = str_replace("\n", "", $cleaned_html);
        return $cleaned_html;
    }

    #
    # Eliminar espacios en exceso de una cadena
    #
    function eliminar_multiples_espacios($texto)
    {
        return preg_replace('/\s\s+/', ' ', $texto);
    }

    #
    # Eliminar dobles comillas y codificar caracteres &; correctamente
    #
    function normalizar_texto($texto)
    {
        $resultado = str_replace('"', "", $texto);            // eliminar doble comilla
        $resultado = str_replace('&amp;', "&", $resultado);   // pasar &amp; a &
        $resultado = str_replace('&lt;', "<", $resultado);
        $resultado = str_replace('&gt;', ">", $resultado);
        $resultado = str_replace('&aacute;', "á", $resultado);
        $resultado = str_replace('&eacute;', "é", $resultado);
        $resultado = str_replace('&iacute;', "í", $resultado);
        $resultado = str_replace('&oacute;', "ó", $resultado);
        $resultado = str_replace('&uacute;', "ú", $resultado);
        $resultado = str_replace('&quot;', "'", $resultado);

        return $resultado;
    }

    #
    # Eliminar elementos vacios de un array
    #
    function eliminar_vacios_array($array)
    {
        return array_values(array_diff($array, array('','""',null,false)));
    }

    #
    # Split de una cadena separada por comas y acotada por dobles comillas.
    # Ejemplo:  "esto, es",el ejemplo,1,"para que sirve",esto
    #
    function split_cadena_acotada($texto)
    {
        preg_match_all("([^,\"]*,|\"[^\"]*\",)", $texto.",", $matching_data);
        array_walk($matching_data[0], 'trim_value');
        return $matching_data[0];
    }
    function trim_value(&$value)
    {
        $value = trim($value);
        $value = trim($value,',');
        $value = trim($value,'"');
    }
         
    #
    # si está habilitado DEBUG, escribe una variable por pantalla, sino, no muestra nada
    #
    function http_verbose($variable)
    {
         if (DEBUG_CURL)
         {
             var_dump($variable);
             echo "<br>\n";
         }
    }

    #
    # escribe por pantalla un texto formateado "fase. texto"
    #
    function http_mensaje($fase, $texto)
    {
         echo $fase.". ".$texto." <br>\n";
    }

    #
    # devolver un objeto curl parametrizado por defecto
    #
    function http_get_curl($agent_index = -1, $header = "-")
    {

        $USER_AGENTS = array(
            "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_4; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.127 Safari/533.4",
            "Mozilla/5.0 (X11; U; Linux i686; es-AR; rv:1.9.2.16) Gecko/20110323 Ubuntu/10.10 (maverick) Firefox/3.6.16",
            "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.8) Gecko/2009033011 Gentoo Firefox/3.0.8",
            "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.0.7) Gecko/2009032813 Iceweasel/3.0.6 (Debian-3.0.6-1)",
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; pl; rv:1.9) Gecko/2008052906 Firefox/3.0",
            "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)",
            "Mozilla/4.8 [en] (Windows NT 6.0; U)",
            "Opera/9.25 (Windows NT 6.0; U; en)"
        );

        $HTTP_HEADER = array(
            "Accept-Language: es-ES,es;q=0.8",
            "Accept: application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5",
            "Keep-Alive: 115",
            "Connection: keep-alive",
            "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3",
            "Expect:"
        );

        $handler = curl_init();

        curl_setopt($handler, CURLOPT_VERBOSE, DEBUG_CURL);

        if ($agent_index == -1)
        {
            curl_setopt($handler, CURLOPT_USERAGENT, $USER_AGENTS[rand(0, count($USER_AGENTS)- 1)]);
        }
        else
        {
            curl_setopt($handler, CURLOPT_USERAGENT, $USER_AGENTS[$agent_index]);
        }

        if ($header == "-")
        {
            curl_setopt($handler, CURLOPT_HTTPHEADER, $HTTP_HEADER);
        }
        elseif (is_array($header))
        {
            curl_setopt($handler, CURLOPT_HTTPHEADER, $header);
        }
        if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == '' || ini_get('safe_mode') == 'Off') )
        {
            curl_setopt($handler, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($handler, CURLOPT_MAXREDIRS, 4);
        }
        curl_setopt($handler, CURLOPT_POST, FALSE);                           // Inicialmente configurado con GET
        curl_setopt($handler, CURLOPT_HEADER, FALSE);                         // Devuelve las cabeceras junto con la respuesta del servidor
        curl_setopt($handler, CURLOPT_TIMEOUT, CURL_TIMEOUT );
        curl_setopt($handler, CURLOPT_COOKIEJAR, getcwd().'/'.COOKIE);        // Fichero donde se guardan Cookies al cerrar conexion
        curl_setopt($handler, CURLOPT_COOKIEFILE, getcwd().'/'.COOKIE);       // Fichero donde se almacenan las Cookies
        curl_setopt($handler, CURLOPT_CONNECTTIMEOUT, FALSE);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, TRUE);                  // Redirige la salida de curl_exec a una variable
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, FALSE);                 // Que no compruebe SSL
        curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, FALSE);                 // Pasando de SSL

        return $handler;

        }

        #
        # curl exec modificado para realizar redirect en un hosting que no lo permite
        #
        function http_curl_exec($handler)
        {
            if (ini_get('open_basedir') == '' && (ini_get('safe_mode') == '' || ini_get('safe_mode') == 'Off') )
            {
                return curl_exec($handler);
            }
            else
            {
                return curl_redir_exec($handler);
            }
        }

        //
        //  http://www.edmondscommerce.co.uk/curl/php-curl-curlopt_followlocation-and-open_basedir-or-safe-mode/
        //  follow on location problems workaround
        //
        function curl_redir_exec($ch)
        {

          static $curl_loops = 0;
          static $curl_max_loops = 4;
          if ($curl_loops++ >= $curl_max_loops)
          {
             $curl_loops = 0;
             return FALSE;
          }

          curl_setopt($ch, CURLOPT_HEADER, true);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          $data = curl_exec($ch);
          list($header, $data) = explode("\n\n", $data, 2);
          $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
          if ($http_code == 301 || $http_code == 302)
          {
              $matches = array();
              preg_match('/Location:(.*?)\n/', $header, $matches);
              $url = @parse_url(trim(array_pop($matches)));

              if (!$url)
              {
              //couldn't process the url to redirect to
              $curl_loops = 0;
              return $data;
          }

          $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
          if (!$url['scheme'])
            $url['scheme'] = $last_url['scheme'];
          if (!$url['host'])
            $url['host'] = $last_url['host'];
          if (!$url['path'])
            $url['path'] = $last_url['path'];

          $new_url = $url['scheme'] . '://' . $url['host'] . $url['path'] . ($url['query']?'?'.$url['query']:'');
          curl_setopt($ch, CURLOPT_URL, $new_url);
          return curl_redir_exec($ch);

          }
          else
          {
              $curl_loops=0;
              return $data;
          }
        }

?>
