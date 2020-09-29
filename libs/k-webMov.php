<?php

class webMovs
{
    public static function rebuildMovs()
    {
        $conx = db_connect($GLOBALS['dbase']);
        $n = 0; //Numero delle righe inserite
        $Query = "SELECT U_BARDR.ID, U_BARDR.ID_TESTA, U_BARDR.ID_RIFRIGA,
                      U_BARDR.DATADOC, U_BARDR.TIPODOC, U_BARDR.CODICEARTI,
                      U_BARDR.LOTTO, U_BARDR.QUANTITA, U_BARDR.MAGPARTENZ,
                      U_BARDR.MAGARRIVO, U_BARDT.NUMERODOCF
                    FROM U_BARDR INNER JOIN U_BARDT ON U_BARDT.ID=U_BARDR.ID_TESTA
                    WHERE U_BARDT.DEL!=1 AND U_BARDR.DEL!=1 AND U_BARDR.CODICEARTI!='' ";
        $result = db_query($conx, $Query) or die(mysql_error());
        while ($rw = mysql_fetch_object($result)) {
            ++$n;
            self::insWebMov($rw->ID_TESTA, $rw->ID, $rw->ID_RIFRIGA, $rw->TIPODOC, $rw->DATADOC,
                            $rw->CODICEARTI, $rw->LOTTO, $rw->QUANTITA, $rw->MAGPARTENZ, $rw->MAGARRIVO, $rw->NUMERODOCF);
        }
        mysqli_free_result($result);
        db_close($conx);
        return $n;
    }

    public static function insWebMov($idHead, $idRow, $idPadre, $tipoDoc, $dataDoc, $articolo, $lotto, $qta, $magP, $magA, $RifDoc)
    {
        $conx = db_connect($GLOBALS['dbase']);
        $magMov = $magP;
        $qtaCar = 0;
        $qtaScar = 1;
        if (is_string($dataDoc)) {
            $dtDoc = date('Y-m-d', strtotime($dataDoc));
        } else {
            $dtDoc = date('Y-m-d', $dataDoc);
        }
        if (strcmp($tipoDoc, 'KS') == 0) {
            $magMov = $magA;
            $qtaCar = 1;
            $qtaScar = 0;
        }
        $Query = "INSERT INTO U_WEBMOVS
								(ID_TESTA, ID_RIGA, ID_RIGA_P, MAGAZZINO, ARTICOLO, LOTTO, QUANTITA, QTA_CAR, QTA_SCAR, RIFDOC, DATAMOV)
								VALUES
								($idHead, $idRow, $idPadre, '$magMov', '$articolo', '$lotto', $qta, $qtaCar, $qtaScar, '$RifDoc', CAST('".$dtDoc."' AS DATE)) ";
        $qx = db_query($conx, $Query) or die(mysql_error());
        //db_close($conx);
        return $qx;
    }

    public static function deleteAll()
    {
        $conx = db_connect($GLOBALS['dbase']);
        $Query = 'DELETE FROM U_WEBMOVS';
        $qx = db_query($conx, $Query) or die(mysql_error());
        db_close($conx);
        return $qx;
    }

    public static function delWebMov($idHead, $idPadre)
    {
        $conx = db_connect($GLOBALS['dbase']);
        if (!empty($idHead)) {
            $Query = "DELETE FROM U_WEBMOVS WHERE ID_TESTA=$idHead";
        } else {
            $Query = "DELETE FROM U_WEBMOVS WHERE ID_RIGA_P=$idPadre";
        }
        $qx = db_query($conx, $Query) or die(mysql_error());
        db_close($conx);
        return $qx;
    }

    public static function giacWebMov($magazzino, $articolo, $lotto)
    {
        $conx = db_connect($GLOBALS['dbase']);
        if (empty($lotto)) {
            $Query = "SELECT SUM(QUANTITA*QTA_CAR) AS CARICO, SUM(QUANTITA*QTA_SCAR) AS SCARICO
  								FROM U_WEBMOVS
  								WHERE U_WEBMOVS.MAGAZZINO='$magazzino' AND U_WEBMOVS.ARTICOLO='$articolo'
  								GROUP BY MAGAZZINO, ARTICOLO, LOTTO ";
        } else {
            $Query = "SELECT SUM(QUANTITA*QTA_CAR) AS CARICO, SUM(QUANTITA*QTA_SCAR) AS SCARICO
  								FROM U_WEBMOVS
  								WHERE U_WEBMOVS.MAGAZZINO='$magazzino' AND U_WEBMOVS.ARTICOLO='$articolo'
  									AND U_WEBMOVS.LOTTO='$lotto'
  								GROUP BY MAGAZZINO, ARTICOLO, LOTTO ";
        }
        $result = db_query($conx, $Query) or die(mysql_error());
        if (mysql_num_rows($result) != 0) {
            $rw = mysql_fetch_object($result);
            $carico = $rw->CARICO;
            $scarico = $rw->SCARICO;
            $giac = $carico - $scarico;
        } else {
            $giac = 0;
        }
        mysqli_free_result($result);
        //db_close($conx);
        return $giac;
    }

    public static function getWebMovs($magazzino, $articolo)
    {
        $conx = db_connect($GLOBALS['dbase']);
        $aMovs = array();
        $i = 0;
        $Query = "SELECT MAGAZZINO, ARTICOLO, LOTTO, QUANTITA*QTA_CAR AS CARICO, QUANTITA*QTA_SCAR AS SCARICO,
									RIFDOC, DATAMOV
								FROM U_WEBMOVS
								WHERE U_WEBMOVS.MAGAZZINO='$magazzino' AND U_WEBMOVS.ARTICOLO='$articolo'
								ORDER BY DATAMOV, RIFDOC ";
        $result = db_query($conx, $Query) or die(mysql_error());
        while ($rw = mysql_fetch_assoc($result)) {
            $aMovs[$i] = $rw;
            ++$i;
        }
        mysqli_free_result($result);
        //db_close($conx);
        return $aMovs; //Restituisce un array
    }
}
