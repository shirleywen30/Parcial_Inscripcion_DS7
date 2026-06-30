<?php
class ClaveOpenSSL {
    private static $privateFile = __DIR__ . '/keys/private.pem';
    private static $publicFile  = __DIR__ . '/keys/public.pem';

    public static function getPrivateKey() {
        self::configurarOpenSSL();
        if (!file_exists(self::$privateFile)) {
            self::generarClaves();
        }
        return file_exists(self::$privateFile) ? file_get_contents(self::$privateFile) : false;
    }

    public static function getPublicKey() {
        self::configurarOpenSSL();
        if (!file_exists(self::$publicFile)) {
            self::generarClaves();
        }
        return file_exists(self::$publicFile) ? file_get_contents(self::$publicFile) : false;
    }

    private static function configurarOpenSSL() {
        $cnf = self::buscarConfigOpenSSL();
        if ($cnf) {
            putenv('OPENSSL_CONF=' . $cnf);
        }
    }

    private static function buscarConfigOpenSSL() {
        // Buscar en las carpetas de PHP de WAMP
        $candidatos = array_merge(
            glob('C:\\wamp64\\bin\\php\\php*\\extras\\ssl\\openssl.cnf') ?: [],
            [
                PHP_BINDIR . '\\extras\\ssl\\openssl.cnf',
                PHP_BINDIR . '/extras/ssl/openssl.cnf',
                'C:\\wamp64\\bin\\apache\\apache2.4.65\\conf\\openssl.cnf',
            ]
        );

        foreach ($candidatos as $ruta) {
            if (file_exists($ruta)) {
                return $ruta;
            }
        }
        return null;
    }

    private static function generarClaves() {
        $dir = dirname(self::$privateFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $cnf = self::buscarConfigOpenSSL();

        if ($cnf) {
            putenv('OPENSSL_CONF=' . $cnf);
        }

        $opciones = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
        if ($cnf) {
            $opciones['config'] = $cnf;
        }

        $recurso = openssl_pkey_new($opciones);

        if ($recurso === false) {
            error_log('ClaveOpenSSL: no se pudo generar la clave. ' . openssl_error_string());
            return;
        }

        $exportOpciones = $cnf ? ['config' => $cnf] : [];
        openssl_pkey_export($recurso, $private_pem, null, $exportOpciones ?: null);
        $detalles = openssl_pkey_get_details($recurso);

        if ($private_pem && isset($detalles['key'])) {
            file_put_contents(self::$privateFile, $private_pem);
            file_put_contents(self::$publicFile,  $detalles['key']);
        }
    }
}
?>
