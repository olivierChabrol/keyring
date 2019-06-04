<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParamRepository")
 */
class Param
{
    const TYPE  = 1;
    const SITE  = 2;
    const STATE = 3;
    const DEPARTMENT = 4;
    const POSITION = 5;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public static function getTypes()
    {
        $assoc =  array(
            Param::DEPARTMENT => "Department",
            Param::POSITION => "Position",
        Param::SITE  => "Site",
        Param::STATE => "State",
        Param::TYPE  => "Type",);
        return $assoc;
    }

    public static function getNationality()
    {
        $assoc =  array();
        $assoc["AD"]="Andorre";
        $assoc["AE"]="Émirats arabes unis";
        $assoc["AF"]="Afghanistan";
        $assoc["AG"]="Antigua-et-Barbuda";
        $assoc["AI"]="Anguilla";
        $assoc["AL"]="Albanie";
        $assoc["AM"]="Arménie";
        $assoc["AO"]="Angola";
        $assoc["AQ"]="Antarctique";
        $assoc["AR"]="Argentine";
        $assoc["AS"]="Samoa américaine";
        $assoc["AT"]="Autriche";
        $assoc["AU"]="Australie";
        $assoc["AW"]="Aruba";
        $assoc["AX"]="Îles d'Åland";
        $assoc["AZ"]="Azerbaïdjan";
        $assoc["BA"]="Bosnie-Herzégovine";
        $assoc["BB"]="Barbade";
        $assoc["BD"]="Bangladesh";
        $assoc["BE"]="Belgique";
        $assoc["BF"]="Burkina Faso";
        $assoc["BG"]="Bulgarie";
        $assoc["BH"]="Bahreïn";
        $assoc["BI"]="Burundi";
        $assoc["BJ"]="Bénin";
        $assoc["BL"]="Saint-Barthélemy";
        $assoc["BM"]="Bermudes";
        $assoc["BN"]="Brunei Darussalam";
        $assoc["BO"]="Bolivie";
        $assoc["BQ"]="Pays-Bas caribéens";
        $assoc["BR"]="Brésil";
        $assoc["BS"]="Bahamas";
        $assoc["BT"]="Bhoutan";
        $assoc["BV"]="Île Bouvet";
        $assoc["BW"]="Botswana";
        $assoc["BY"]="Bélarus";
        $assoc["BZ"]="Belize";
        $assoc["CA"]="Canada";
        $assoc["CC"]="Îles Cocos (Keeling)";
        $assoc["CD"]="Congo, République démocratique du";
        $assoc["CF"]="République centrafricaine";
        $assoc["CG"]="Congo";
        $assoc["CH"]="Suisse";
        $assoc["CI"]="Côte d'Ivoire";
        $assoc["CK"]="Îles Cook";
        $assoc["CL"]="Chili";
        $assoc["CM"]="Cameroun";
        $assoc["CN"]="Chine";
        $assoc["CO"]="Colombie";
        $assoc["CR"]="Costa Rica";
        $assoc["CU"]="Cuba";
        $assoc["CV"]="Cap-Vert";
        $assoc["CW"]="Curaçao";
        $assoc["CX"]="Île Christmas";
        $assoc["CY"]="Chypre";
        $assoc["CZ"]="République tchèque";
        $assoc["DE"]="Allemagne";
        $assoc["DJ"]="Djibouti";
        $assoc["DK"]="Danemark";
        $assoc["DM"]="Dominique";
        $assoc["DO"]="République dominicaine";
        $assoc["DZ"]="Algérie";
        $assoc["EC"]="Équateur";
        $assoc["EE"]="Estonie";
        $assoc["EG"]="Égypte";
        $assoc["EH"]="Sahara Occidental";
        $assoc["ER"]="Érythrée";
        $assoc["ES"]="Espagne";
        $assoc["ET"]="Éthiopie";
        $assoc["FI"]="Finlande";
        $assoc["FJ"]="Fidji";
        $assoc["FK"]="Îles Malouines";
        $assoc["FM"]="Micronésie, États fédérés de";
        $assoc["FO"]="Îles Féroé";
        $assoc["FR"]="France";
        $assoc["GA"]="Gabon";
        $assoc["GB"]="Royaume-Uni";
        $assoc["GD"]="Grenade";
        $assoc["GE"]="Géorgie";
        $assoc["GF"]="Guyane française";
        $assoc["GG"]="Guernesey";
        $assoc["GH"]="Ghana";
        $assoc["GI"]="Gibraltar";
        $assoc["GL"]="Groenland";
        $assoc["GM"]="Gambie";
        $assoc["GN"]="Guinée";
        $assoc["GP"]="Guadeloupe";
        $assoc["GQ"]="Guinée équatoriale";
        $assoc["GR"]="Grèce";
        $assoc["GS"]="Géorgie du Sud et les îles Sandwich du Sud";
        $assoc["GT"]="Guatemala";
        $assoc["GU"]="Guam";
        $assoc["GW"]="Guinée-Bissau";
        $assoc["GY"]="Guyana";
        $assoc["HK"]="Hong Kong";
        $assoc["HM"]="Îles Heard et McDonald";
        $assoc["HN"]="Honduras";
        $assoc["HR"]="Croatie";
        $assoc["HT"]="Haïti";
        $assoc["HU"]="Hongrie";
        $assoc["ID"]="Indonésie";
        $assoc["IE"]="Irlande";
        $assoc["IL"]="Israël";
        $assoc["IM"]="Île de Man";
        $assoc["IN"]="Inde";
        $assoc["IO"]="Territoire britannique de l'océan Indien";
        $assoc["IQ"]="Irak";
        $assoc["IR"]="Iran";
        $assoc["IS"]="Islande";
        $assoc["IT"]="Italie";
        $assoc["JE"]="Jersey";
        $assoc["JM"]="Jamaïque";
        $assoc["JO"]="Jordanie";
        $assoc["JP"]="Japon";
        $assoc["KE"]="Kenya";
        $assoc["KG"]="Kirghizistan";
        $assoc["KH"]="Cambodge";
        $assoc["KI"]="Kiribati";
        $assoc["KM"]="Comores";
        $assoc["KN"]="Saint-Kitts-et-Nevis";
        $assoc["KP"]="Corée du Nord";
        $assoc["KR"]="Corée du Sud";
        $assoc["KW"]="Koweït";
        $assoc["KY"]="Îles Caïmans";
        $assoc["KZ"]="Kazakhstan";
        $assoc["LA"]="Laos";
        $assoc["LB"]="Liban";
        $assoc["LC"]="Sainte-Lucie";
        $assoc["LI"]="Liechtenstein";
        $assoc["LK"]="Sri Lanka";
        $assoc["LR"]="Libéria";
        $assoc["LS"]="Lesotho";
        $assoc["LT"]="Lituanie";
        $assoc["LU"]="Luxembourg";
        $assoc["LV"]="Lettonie";
        $assoc["LY"]="Libye";
        $assoc["MA"]="Maroc";
        $assoc["MC"]="Monaco";
        $assoc["MD"]="Moldavie";
        $assoc["ME"]="Monténégro";
        $assoc["MF"]="Saint-Martin (France)";
        $assoc["MG"]="Madagascar";
        $assoc["MH"]="Îles Marshall";
        $assoc["MK"]="Macédoine";
        $assoc["ML"]="Mali";
        $assoc["MM"]="Myanmar";
        $assoc["MN"]="Mongolie";
        $assoc["MO"]="Macao";
        $assoc["MP"]="Mariannes du Nord";
        $assoc["MQ"]="Martinique";
        $assoc["MR"]="Mauritanie";
        $assoc["MS"]="Montserrat";
        $assoc["MT"]="Malte";
        $assoc["MU"]="Maurice";
        $assoc["MV"]="Maldives";
        $assoc["MW"]="Malawi";
        $assoc["MX"]="Mexique";
        $assoc["MY"]="Malaisie";
        $assoc["MZ"]="Mozambique";
        $assoc["NA"]="Namibie";
        $assoc["NC"]="Nouvelle-Calédonie";
        $assoc["NE"]="Niger";
        $assoc["NF"]="Île Norfolk";
        $assoc["NG"]="Nigeria";
        $assoc["NI"]="Nicaragua";
        $assoc["NL"]="Pays-Bas";
        $assoc["NO"]="Norvège";
        $assoc["NP"]="Népal";
        $assoc["NR"]="Nauru";
        $assoc["NU"]="Niue";
        $assoc["NZ"]="Nouvelle-Zélande";
        $assoc["OM"]="Oman";
        $assoc["PA"]="Panama";
        $assoc["PE"]="Pérou";
        $assoc["PF"]="Polynésie française";
        $assoc["PG"]="Papouasie-Nouvelle-Guinée";
        $assoc["PH"]="Philippines";
        $assoc["PK"]="Pakistan";
        $assoc["PL"]="Pologne";
        $assoc["PM"]="Saint-Pierre-et-Miquelon";
        $assoc["PN"]="Pitcairn";
        $assoc["PR"]="Puerto Rico";
        $assoc["PS"]="Palestine";
        $assoc["PT"]="Portugal";
        $assoc["PW"]="Palau";
        $assoc["PY"]="Paraguay";
        $assoc["QA"]="Qatar";
        $assoc["RE"]="Réunion";
        $assoc["RO"]="Roumanie";
        $assoc["RS"]="Serbie";
        $assoc["RU"]="Russie";
        $assoc["RW"]="Rwanda";
        $assoc["SA"]="Arabie saoudite";
        $assoc["SB"]="Îles Salomon";
        $assoc["SC"]="Seychelles";
        $assoc["SD"]="Soudan";
        $assoc["SE"]="Suède";
        $assoc["SG"]="Singapour";
        $assoc["SH"]="Sainte-Hélène";
        $assoc["SI"]="Slovénie";
        $assoc["SJ"]="Svalbard et île de Jan Mayen";
        $assoc["SK"]="Slovaquie";
        $assoc["SL"]="Sierra Leone";
        $assoc["SM"]="Saint-Marin";
        $assoc["SN"]="Sénégal";
        $assoc["SO"]="Somalie";
        $assoc["SR"]="Suriname";
        $assoc["SS"]="Soudan du Sud";
        $assoc["ST"]="Sao Tomé-et-Principe";
        $assoc["SV"]="El Salvador";
        $assoc["SX"]="Saint-Martin (Pays-Bas)";
        $assoc["SY"]="Syrie";
        $assoc["SZ"]="Swaziland";
        $assoc["TC"]="Îles Turks et Caicos";
        $assoc["TD"]="Tchad";
        $assoc["TF"]="Terres australes françaises";
        $assoc["TG"]="Togo";
        $assoc["TH"]="Thaïlande";
        $assoc["TJ"]="Tadjikistan";
        $assoc["TK"]="Tokelau";
        $assoc["TL"]="Timor-Leste";
        $assoc["TM"]="Turkménistan";
        $assoc["TN"]="Tunisie";
        $assoc["TO"]="Tonga";
        $assoc["TR"]="Turquie";
        $assoc["TT"]="Trinité-et-Tobago";
        $assoc["TV"]="Tuvalu";
        $assoc["TW"]="Taïwan";
        $assoc["TZ"]="Tanzanie";
        $assoc["UA"]="Ukraine";
        $assoc["UG"]="Ouganda";
        $assoc["UM"]="Îles mineures éloignées des États-Unis";
        $assoc["US"]="États-Unis";
        $assoc["UY"]="Uruguay";
        $assoc["UZ"]="Ouzbékistan";
        $assoc["VA"]="Vatican";
        $assoc["VC"]="Saint-Vincent-et-les-Grenadines";
        $assoc["VE"]="Venezuela";
        $assoc["VG"]="Îles Vierges britanniques";
        $assoc["VI"]="Îles Vierges américaines";
        $assoc["VN"]="Vietnam";
        $assoc["VU"]="Vanuatu";
        $assoc["WF"]="Îles Wallis-et-Futuna";
        $assoc["WS"]="Samoa";
        $assoc["YE"]="Yémen";
        $assoc["YT"]="Mayotte";
        $assoc["ZA"]="Afrique du Sud";
        $assoc["ZM"]="Zambie";
        $assoc["ZW"]="Zimbabwe";
        return $assoc;
    }
}
