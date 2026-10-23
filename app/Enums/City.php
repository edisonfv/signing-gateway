<?php

namespace App\Enums;

use Illuminate\Support\Str;
use Spatie\Enum\Laravel\Enum;

/**
 * Ciudades del Ecuador agrupadas por provincia.
 *
 * El valor es "PROVINCIA:CIUDAD" porque hay nombres repetidos entre provincias
 * (BOLÍVAR, OLMEDO, LA CONCORDIA, PICHINCHA). Al proveedor se envía providerName().
 *
 * Archivo generado por `php artisan gateway:catalogs:locations`. No editar a mano.
 * Fuente: https://docs.firmaseguraec.com/lista-de-ciudades-y-provincias-1820331m0
 *
 * @method static self azuayCamiloPonceEnriquez()
 * @method static self azuayChordeleg()
 * @method static self azuayCuenca()
 * @method static self azuayElPan()
 * @method static self azuayGiron()
 * @method static self azuayGuachapala()
 * @method static self azuayGualaceo()
 * @method static self azuayNabon()
 * @method static self azuayOna()
 * @method static self azuayPaute()
 * @method static self azuayPucara()
 * @method static self azuaySanFernando()
 * @method static self azuaySantaIsabel()
 * @method static self azuaySevillaDeOro()
 * @method static self azuaySigsig()
 * @method static self bolivarCaluma()
 * @method static self bolivarChillanes()
 * @method static self bolivarChimbo()
 * @method static self bolivarEcheandia()
 * @method static self bolivarGuaranda()
 * @method static self bolivarLasNaves()
 * @method static self bolivarSanMiguel()
 * @method static self canarAzogues()
 * @method static self canarBiblian()
 * @method static self canarCanar()
 * @method static self canarDeleg()
 * @method static self canarElTambo()
 * @method static self canarLaTroncal()
 * @method static self canarSuscal()
 * @method static self carchiBolivar()
 * @method static self carchiEspejo()
 * @method static self carchiMira()
 * @method static self carchiMontufar()
 * @method static self carchiSanPedroDeHuaca()
 * @method static self carchiTulcan()
 * @method static self chimborazoAlausi()
 * @method static self chimborazoChambo()
 * @method static self chimborazoChunchi()
 * @method static self chimborazoColta()
 * @method static self chimborazoCumanda()
 * @method static self chimborazoGuamote()
 * @method static self chimborazoGuano()
 * @method static self chimborazoPallatanga()
 * @method static self chimborazoPenipe()
 * @method static self chimborazoRiobamba()
 * @method static self cotopaxiLaMana()
 * @method static self cotopaxiLasso()
 * @method static self cotopaxiLatacunga()
 * @method static self cotopaxiMulalo()
 * @method static self cotopaxiPangua()
 * @method static self cotopaxiPujili()
 * @method static self cotopaxiSalcedo()
 * @method static self cotopaxiSaquisili()
 * @method static self cotopaxiSigchos()
 * @method static self cotopaxiTanicuchi()
 * @method static self elOroArenillas()
 * @method static self elOroAtahualpa()
 * @method static self elOroBalsas()
 * @method static self elOroChilla()
 * @method static self elOroElGuabo()
 * @method static self elOroHuaquillas()
 * @method static self elOroLasLajas()
 * @method static self elOroMachala()
 * @method static self elOroMarcabeli()
 * @method static self elOroPasaje()
 * @method static self elOroPinas()
 * @method static self elOroPortovelo()
 * @method static self elOroSantaRosa()
 * @method static self elOroZaruma()
 * @method static self esmeraldasAtacames()
 * @method static self esmeraldasEloyAlfaro()
 * @method static self esmeraldasEsmeraldas()
 * @method static self esmeraldasLaConcordia()
 * @method static self esmeraldasMuisne()
 * @method static self esmeraldasQuininde()
 * @method static self esmeraldasRioverde()
 * @method static self esmeraldasSanLorenzo()
 * @method static self galapagosIsabela()
 * @method static self galapagosSanCristobal()
 * @method static self galapagosSantaCruz()
 * @method static self guayasAlfredoBaquerizoMorenoJujan()
 * @method static self guayasBalao()
 * @method static self guayasBalzar()
 * @method static self guayasColimes()
 * @method static self guayasCoronelMarcelinoMariduena()
 * @method static self guayasDaule()
 * @method static self guayasDuran()
 * @method static self guayasElEmpalme()
 * @method static self guayasElTriunfo()
 * @method static self guayasGeneralAntonioElizaldeBucay()
 * @method static self guayasGuayaquil()
 * @method static self guayasIsidroAyora()
 * @method static self guayasLomasDeSargentillo()
 * @method static self guayasMilagro()
 * @method static self guayasNaranjal()
 * @method static self guayasNaranjito()
 * @method static self guayasNobol()
 * @method static self guayasPalestina()
 * @method static self guayasPedroCarbo()
 * @method static self guayasPlayas()
 * @method static self guayasSalitreUrbinaJado()
 * @method static self guayasSamborondon()
 * @method static self guayasSanJacintoDeYaguachi()
 * @method static self guayasSantaLucia()
 * @method static self guayasSimonBolivar()
 * @method static self imbaburaAntonioAnte()
 * @method static self imbaburaCotacachi()
 * @method static self imbaburaIbarra()
 * @method static self imbaburaOtavalo()
 * @method static self imbaburaPimampiro()
 * @method static self imbaburaSanMiguelDeUrcuqui()
 * @method static self lojaCalvas()
 * @method static self lojaCariamanga()
 * @method static self lojaCatamayo()
 * @method static self lojaCelica()
 * @method static self lojaChaguarpamba()
 * @method static self lojaEspindola()
 * @method static self lojaGonzanama()
 * @method static self lojaLoja()
 * @method static self lojaMacara()
 * @method static self lojaOlmedo()
 * @method static self lojaPaltas()
 * @method static self lojaPindal()
 * @method static self lojaPuyango()
 * @method static self lojaQuilanga()
 * @method static self lojaSaraguro()
 * @method static self lojaSozoranga()
 * @method static self lojaZapotillo()
 * @method static self losRiosBaba()
 * @method static self losRiosBabahoyo()
 * @method static self losRiosBuenaFe()
 * @method static self losRiosMocache()
 * @method static self losRiosMontalvo()
 * @method static self losRiosPalenque()
 * @method static self losRiosPuebloviejo()
 * @method static self losRiosQuevedo()
 * @method static self losRiosQuinsaloma()
 * @method static self losRiosUrdaneta()
 * @method static self losRiosValencia()
 * @method static self losRiosVentanas()
 * @method static self losRiosVinces()
 * @method static self manabi24DeMayo()
 * @method static self manabiBolivar()
 * @method static self manabiChone()
 * @method static self manabiElCarmen()
 * @method static self manabiFlavioAlfaro()
 * @method static self manabiJama()
 * @method static self manabiJaramijo()
 * @method static self manabiJipijapa()
 * @method static self manabiJunin()
 * @method static self manabiManta()
 * @method static self manabiMontecristi()
 * @method static self manabiOlmedo()
 * @method static self manabiPajan()
 * @method static self manabiPedernales()
 * @method static self manabiPichincha()
 * @method static self manabiPortoviejo()
 * @method static self manabiPuertoLopez()
 * @method static self manabiRocafuerte()
 * @method static self manabiSanVicente()
 * @method static self manabiSantaAna()
 * @method static self manabiSucre()
 * @method static self manabiTosagua()
 * @method static self moronaSantiagoGualaquiza()
 * @method static self moronaSantiagoHuamboya()
 * @method static self moronaSantiagoLimonIndanza()
 * @method static self moronaSantiagoLogrono()
 * @method static self moronaSantiagoMacas()
 * @method static self moronaSantiagoMorona()
 * @method static self moronaSantiagoPabloSexto()
 * @method static self moronaSantiagoPalora()
 * @method static self moronaSantiagoSanJuanBosco()
 * @method static self moronaSantiagoSantiago()
 * @method static self moronaSantiagoSucua()
 * @method static self moronaSantiagoTaisha()
 * @method static self moronaSantiagoTiwintza()
 * @method static self napoArchidona()
 * @method static self napoCarlosJulioArosemenaTola()
 * @method static self napoElChaco()
 * @method static self napoQuijos()
 * @method static self napoTena()
 * @method static self orellanaAguarico()
 * @method static self orellanaElCoca()
 * @method static self orellanaLaJoyaDeLosSachas()
 * @method static self orellanaLoreto()
 * @method static self orellanaOrellana()
 * @method static self pastazaArajuno()
 * @method static self pastazaMera()
 * @method static self pastazaPastaza()
 * @method static self pastazaPuyo()
 * @method static self pastazaSantaClara()
 * @method static self pastazaShell()
 * @method static self pichinchaCayambe()
 * @method static self pichinchaMejia()
 * @method static self pichinchaPedroMoncayo()
 * @method static self pichinchaPedroVicenteMaldonado()
 * @method static self pichinchaPuertoQuito()
 * @method static self pichinchaQuito()
 * @method static self pichinchaRuminahui()
 * @method static self pichinchaSanMiguelDeLosBancos()
 * @method static self pichinchaMachachi()
 * @method static self pichinchaSangolqui()
 * @method static self santaElenaLaLibertad()
 * @method static self santaElenaSalinas()
 * @method static self santaElenaSantaElena()
 * @method static self santoDomingoDeLosTsachilasLaConcordia()
 * @method static self santoDomingoDeLosTsachilasSantoDomingo()
 * @method static self sucumbiosCascales()
 * @method static self sucumbiosCuyabeno()
 * @method static self sucumbiosGonzaloPizarro()
 * @method static self sucumbiosLagoAgrio()
 * @method static self sucumbiosPutumayo()
 * @method static self sucumbiosShushufindi()
 * @method static self sucumbiosSucumbios()
 * @method static self tungurahuaAmbato()
 * @method static self tungurahuaBanosDeAguaSanta()
 * @method static self tungurahuaCevallos()
 * @method static self tungurahuaMocha()
 * @method static self tungurahuaPatate()
 * @method static self tungurahuaPillaro()
 * @method static self tungurahuaQuero()
 * @method static self tungurahuaRioNegro()
 * @method static self tungurahuaSanPedroDePelileo()
 * @method static self tungurahuaSantiagoDePillaro()
 * @method static self tungurahuaTisaleo()
 * @method static self zamoraChinchipeCentinelaDelCondor()
 * @method static self zamoraChinchipeChinchipe()
 * @method static self zamoraChinchipeElPangui()
 * @method static self zamoraChinchipeNangaritza()
 * @method static self zamoraChinchipePalanda()
 * @method static self zamoraChinchipePaquisha()
 * @method static self zamoraChinchipeYacuambi()
 * @method static self zamoraChinchipeYantzaza()
 * @method static self zamoraChinchipeZamora()
 */
final class City extends Enum
{
    public function province(): Province
    {
        return Province::from(Str::before($this->value, ':'));
    }

    public function providerName(): string
    {
        return Str::after($this->value, ':');
    }

    /**
     * @return array<int, self>
     */
    public static function forProvince(Province $province): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $city) => $city->province()->equals($province),
        ));
    }

    protected static function values(): array
    {
        return [
            'azuayCamiloPonceEnriquez' => 'AZUAY:CAMILO PONCE ENRÍQUEZ',
            'azuayChordeleg' => 'AZUAY:CHORDELEG',
            'azuayCuenca' => 'AZUAY:CUENCA',
            'azuayElPan' => 'AZUAY:EL PAN',
            'azuayGiron' => 'AZUAY:GIRÓN',
            'azuayGuachapala' => 'AZUAY:GUACHAPALA',
            'azuayGualaceo' => 'AZUAY:GUALACEO',
            'azuayNabon' => 'AZUAY:NABÓN',
            'azuayOna' => 'AZUAY:OÑA',
            'azuayPaute' => 'AZUAY:PAUTE',
            'azuayPucara' => 'AZUAY:PUCARÁ',
            'azuaySanFernando' => 'AZUAY:SAN FERNANDO',
            'azuaySantaIsabel' => 'AZUAY:SANTA ISABEL',
            'azuaySevillaDeOro' => 'AZUAY:SEVILLA DE ORO',
            'azuaySigsig' => 'AZUAY:SIGSIG',
            'bolivarCaluma' => 'BOLÍVAR:CALUMA',
            'bolivarChillanes' => 'BOLÍVAR:CHILLANES',
            'bolivarChimbo' => 'BOLÍVAR:CHIMBO',
            'bolivarEcheandia' => 'BOLÍVAR:ECHEANDÍA',
            'bolivarGuaranda' => 'BOLÍVAR:GUARANDA',
            'bolivarLasNaves' => 'BOLÍVAR:LAS NAVES',
            'bolivarSanMiguel' => 'BOLÍVAR:SAN MIGUEL',
            'canarAzogues' => 'CAÑAR:AZOGUES',
            'canarBiblian' => 'CAÑAR:BIBLIÁN',
            'canarCanar' => 'CAÑAR:CAÑAR',
            'canarDeleg' => 'CAÑAR:DÉLEG',
            'canarElTambo' => 'CAÑAR:EL TAMBO',
            'canarLaTroncal' => 'CAÑAR:LA TRONCAL',
            'canarSuscal' => 'CAÑAR:SUSCAL',
            'carchiBolivar' => 'CARCHI:BOLÍVAR',
            'carchiEspejo' => 'CARCHI:ESPEJO',
            'carchiMira' => 'CARCHI:MIRA',
            'carchiMontufar' => 'CARCHI:MONTÚFAR',
            'carchiSanPedroDeHuaca' => 'CARCHI:SAN PEDRO DE HUACA',
            'carchiTulcan' => 'CARCHI:TULCÁN',
            'chimborazoAlausi' => 'CHIMBORAZO:ALAUSÍ',
            'chimborazoChambo' => 'CHIMBORAZO:CHAMBO',
            'chimborazoChunchi' => 'CHIMBORAZO:CHUNCHI',
            'chimborazoColta' => 'CHIMBORAZO:COLTA',
            'chimborazoCumanda' => 'CHIMBORAZO:CUMANDÁ',
            'chimborazoGuamote' => 'CHIMBORAZO:GUAMOTE',
            'chimborazoGuano' => 'CHIMBORAZO:GUANO',
            'chimborazoPallatanga' => 'CHIMBORAZO:PALLATANGA',
            'chimborazoPenipe' => 'CHIMBORAZO:PENIPE',
            'chimborazoRiobamba' => 'CHIMBORAZO:RIOBAMBA',
            'cotopaxiLaMana' => 'COTOPAXI:LA MANÁ',
            'cotopaxiLasso' => 'COTOPAXI:LASSO',
            'cotopaxiLatacunga' => 'COTOPAXI:LATACUNGA',
            'cotopaxiMulalo' => 'COTOPAXI:MULALÓ',
            'cotopaxiPangua' => 'COTOPAXI:PANGUA',
            'cotopaxiPujili' => 'COTOPAXI:PUJILÍ',
            'cotopaxiSalcedo' => 'COTOPAXI:SALCEDO',
            'cotopaxiSaquisili' => 'COTOPAXI:SAQUISILÍ',
            'cotopaxiSigchos' => 'COTOPAXI:SIGCHOS',
            'cotopaxiTanicuchi' => 'COTOPAXI:TANICUCHÍ',
            'elOroArenillas' => 'EL ORO:ARENILLAS',
            'elOroAtahualpa' => 'EL ORO:ATAHUALPA',
            'elOroBalsas' => 'EL ORO:BALSAS',
            'elOroChilla' => 'EL ORO:CHILLA',
            'elOroElGuabo' => 'EL ORO:EL GUABO',
            'elOroHuaquillas' => 'EL ORO:HUAQUILLAS',
            'elOroLasLajas' => 'EL ORO:LAS LAJAS',
            'elOroMachala' => 'EL ORO:MACHALA',
            'elOroMarcabeli' => 'EL ORO:MARCABELÍ',
            'elOroPasaje' => 'EL ORO:PASAJE',
            'elOroPinas' => 'EL ORO:PIÑAS',
            'elOroPortovelo' => 'EL ORO:PORTOVELO',
            'elOroSantaRosa' => 'EL ORO:SANTA ROSA',
            'elOroZaruma' => 'EL ORO:ZARUMA',
            'esmeraldasAtacames' => 'ESMERALDAS:ATACAMES',
            'esmeraldasEloyAlfaro' => 'ESMERALDAS:ELOY ALFARO',
            'esmeraldasEsmeraldas' => 'ESMERALDAS:ESMERALDAS',
            'esmeraldasLaConcordia' => 'ESMERALDAS:LA CONCORDIA',
            'esmeraldasMuisne' => 'ESMERALDAS:MUISNE',
            'esmeraldasQuininde' => 'ESMERALDAS:QUININDÉ',
            'esmeraldasRioverde' => 'ESMERALDAS:RIOVERDE',
            'esmeraldasSanLorenzo' => 'ESMERALDAS:SAN LORENZO',
            'galapagosIsabela' => 'GALÁPAGOS:ISABELA',
            'galapagosSanCristobal' => 'GALÁPAGOS:SAN CRISTÓBAL',
            'galapagosSantaCruz' => 'GALÁPAGOS:SANTA CRUZ',
            'guayasAlfredoBaquerizoMorenoJujan' => 'GUAYAS:ALFREDO BAQUERIZO MORENO (JUJAN)',
            'guayasBalao' => 'GUAYAS:BALAO',
            'guayasBalzar' => 'GUAYAS:BALZAR',
            'guayasColimes' => 'GUAYAS:COLIMES',
            'guayasCoronelMarcelinoMariduena' => 'GUAYAS:CORONEL MARCELINO MARIDUEÑA',
            'guayasDaule' => 'GUAYAS:DAULE',
            'guayasDuran' => 'GUAYAS:DURÁN',
            'guayasElEmpalme' => 'GUAYAS:EL EMPALME',
            'guayasElTriunfo' => 'GUAYAS:EL TRIUNFO',
            'guayasGeneralAntonioElizaldeBucay' => 'GUAYAS:GENERAL ANTONIO ELIZALDE (BUCAY)',
            'guayasGuayaquil' => 'GUAYAS:GUAYAQUIL',
            'guayasIsidroAyora' => 'GUAYAS:ISIDRO AYORA',
            'guayasLomasDeSargentillo' => 'GUAYAS:LOMAS DE SARGENTILLO',
            'guayasMilagro' => 'GUAYAS:MILAGRO',
            'guayasNaranjal' => 'GUAYAS:NARANJAL',
            'guayasNaranjito' => 'GUAYAS:NARANJITO',
            'guayasNobol' => 'GUAYAS:NOBOL',
            'guayasPalestina' => 'GUAYAS:PALESTINA',
            'guayasPedroCarbo' => 'GUAYAS:PEDRO CARBO',
            'guayasPlayas' => 'GUAYAS:PLAYAS',
            'guayasSalitreUrbinaJado' => 'GUAYAS:SALITRE (URBINA JADO)',
            'guayasSamborondon' => 'GUAYAS:SAMBORONDÓN',
            'guayasSanJacintoDeYaguachi' => 'GUAYAS:SAN JACINTO DE YAGUACHI',
            'guayasSantaLucia' => 'GUAYAS:SANTA LUCÍA',
            'guayasSimonBolivar' => 'GUAYAS:SIMÓN BOLÍVAR',
            'imbaburaAntonioAnte' => 'IMBABURA:ANTONIO ANTE',
            'imbaburaCotacachi' => 'IMBABURA:COTACACHI',
            'imbaburaIbarra' => 'IMBABURA:IBARRA',
            'imbaburaOtavalo' => 'IMBABURA:OTAVALO',
            'imbaburaPimampiro' => 'IMBABURA:PIMAMPIRO',
            'imbaburaSanMiguelDeUrcuqui' => 'IMBABURA:SAN MIGUEL DE URCUQUÍ',
            'lojaCalvas' => 'LOJA:CALVAS',
            'lojaCariamanga' => 'LOJA:CARIAMANGA',
            'lojaCatamayo' => 'LOJA:CATAMAYO',
            'lojaCelica' => 'LOJA:CELICA',
            'lojaChaguarpamba' => 'LOJA:CHAGUARPAMBA',
            'lojaEspindola' => 'LOJA:ESPÍNDOLA',
            'lojaGonzanama' => 'LOJA:GONZANAMÁ',
            'lojaLoja' => 'LOJA:LOJA',
            'lojaMacara' => 'LOJA:MACARÁ',
            'lojaOlmedo' => 'LOJA:OLMEDO',
            'lojaPaltas' => 'LOJA:PALTAS',
            'lojaPindal' => 'LOJA:PINDAL',
            'lojaPuyango' => 'LOJA:PUYANGO',
            'lojaQuilanga' => 'LOJA:QUILANGA',
            'lojaSaraguro' => 'LOJA:SARAGURO',
            'lojaSozoranga' => 'LOJA:SOZORANGA',
            'lojaZapotillo' => 'LOJA:ZAPOTILLO',
            'losRiosBaba' => 'LOS RÍOS:BABA',
            'losRiosBabahoyo' => 'LOS RÍOS:BABAHOYO',
            'losRiosBuenaFe' => 'LOS RÍOS:BUENA FE',
            'losRiosMocache' => 'LOS RÍOS:MOCACHE',
            'losRiosMontalvo' => 'LOS RÍOS:MONTALVO',
            'losRiosPalenque' => 'LOS RÍOS:PALENQUE',
            'losRiosPuebloviejo' => 'LOS RÍOS:PUEBLOVIEJO',
            'losRiosQuevedo' => 'LOS RÍOS:QUEVEDO',
            'losRiosQuinsaloma' => 'LOS RÍOS:QUINSALOMA',
            'losRiosUrdaneta' => 'LOS RÍOS:URDANETA',
            'losRiosValencia' => 'LOS RÍOS:VALENCIA',
            'losRiosVentanas' => 'LOS RÍOS:VENTANAS',
            'losRiosVinces' => 'LOS RÍOS:VINCES',
            'manabi24DeMayo' => 'MANABÍ:24 DE MAYO',
            'manabiBolivar' => 'MANABÍ:BOLÍVAR',
            'manabiChone' => 'MANABÍ:CHONE',
            'manabiElCarmen' => 'MANABÍ:EL CARMEN',
            'manabiFlavioAlfaro' => 'MANABÍ:FLAVIO ALFARO',
            'manabiJama' => 'MANABÍ:JAMA',
            'manabiJaramijo' => 'MANABÍ:JARAMIJÓ',
            'manabiJipijapa' => 'MANABÍ:JIPIJAPA',
            'manabiJunin' => 'MANABÍ:JUNÍN',
            'manabiManta' => 'MANABÍ:MANTA',
            'manabiMontecristi' => 'MANABÍ:MONTECRISTI',
            'manabiOlmedo' => 'MANABÍ:OLMEDO',
            'manabiPajan' => 'MANABÍ:PAJÁN',
            'manabiPedernales' => 'MANABÍ:PEDERNALES',
            'manabiPichincha' => 'MANABÍ:PICHINCHA',
            'manabiPortoviejo' => 'MANABÍ:PORTOVIEJO',
            'manabiPuertoLopez' => 'MANABÍ:PUERTO LÓPEZ',
            'manabiRocafuerte' => 'MANABÍ:ROCAFUERTE',
            'manabiSanVicente' => 'MANABÍ:SAN VICENTE',
            'manabiSantaAna' => 'MANABÍ:SANTA ANA',
            'manabiSucre' => 'MANABÍ:SUCRE',
            'manabiTosagua' => 'MANABÍ:TOSAGUA',
            'moronaSantiagoGualaquiza' => 'MORONA SANTIAGO:GUALAQUIZA',
            'moronaSantiagoHuamboya' => 'MORONA SANTIAGO:HUAMBOYA',
            'moronaSantiagoLimonIndanza' => 'MORONA SANTIAGO:LIMÓN INDANZA',
            'moronaSantiagoLogrono' => 'MORONA SANTIAGO:LOGROÑO',
            'moronaSantiagoMacas' => 'MORONA SANTIAGO:MACAS',
            'moronaSantiagoMorona' => 'MORONA SANTIAGO:MORONA',
            'moronaSantiagoPabloSexto' => 'MORONA SANTIAGO:PABLO SEXTO',
            'moronaSantiagoPalora' => 'MORONA SANTIAGO:PALORA',
            'moronaSantiagoSanJuanBosco' => 'MORONA SANTIAGO:SAN JUAN BOSCO',
            'moronaSantiagoSantiago' => 'MORONA SANTIAGO:SANTIAGO',
            'moronaSantiagoSucua' => 'MORONA SANTIAGO:SUCÚA',
            'moronaSantiagoTaisha' => 'MORONA SANTIAGO:TAISHA',
            'moronaSantiagoTiwintza' => 'MORONA SANTIAGO:TIWINTZA',
            'napoArchidona' => 'NAPO:ARCHIDONA',
            'napoCarlosJulioArosemenaTola' => 'NAPO:CARLOS JULIO AROSEMENA TOLA',
            'napoElChaco' => 'NAPO:EL CHACO',
            'napoQuijos' => 'NAPO:QUIJOS',
            'napoTena' => 'NAPO:TENA',
            'orellanaAguarico' => 'ORELLANA:AGUARICO',
            'orellanaElCoca' => 'ORELLANA:EL COCA',
            'orellanaLaJoyaDeLosSachas' => 'ORELLANA:LA JOYA DE LOS SACHAS',
            'orellanaLoreto' => 'ORELLANA:LORETO',
            'orellanaOrellana' => 'ORELLANA:ORELLANA',
            'pastazaArajuno' => 'PASTAZA:ARAJUNO',
            'pastazaMera' => 'PASTAZA:MERA',
            'pastazaPastaza' => 'PASTAZA:PASTAZA',
            'pastazaPuyo' => 'PASTAZA:PUYO',
            'pastazaSantaClara' => 'PASTAZA:SANTA CLARA',
            'pastazaShell' => 'PASTAZA:SHELL',
            'pichinchaCayambe' => 'PICHINCHA:CAYAMBE',
            'pichinchaMejia' => 'PICHINCHA:MEJÍA',
            'pichinchaPedroMoncayo' => 'PICHINCHA:PEDRO MONCAYO',
            'pichinchaPedroVicenteMaldonado' => 'PICHINCHA:PEDRO VICENTE MALDONADO',
            'pichinchaPuertoQuito' => 'PICHINCHA:PUERTO QUITO',
            'pichinchaQuito' => 'PICHINCHA:QUITO',
            'pichinchaRuminahui' => 'PICHINCHA:RUMIÑAHUI',
            'pichinchaSanMiguelDeLosBancos' => 'PICHINCHA:SAN MIGUEL DE LOS BANCOS',
            'pichinchaMachachi' => 'PICHINCHA:MACHACHI',
            'pichinchaSangolqui' => 'PICHINCHA:SANGOLQUÍ',
            'santaElenaLaLibertad' => 'SANTA ELENA:LA LIBERTAD',
            'santaElenaSalinas' => 'SANTA ELENA:SALINAS',
            'santaElenaSantaElena' => 'SANTA ELENA:SANTA ELENA',
            'santoDomingoDeLosTsachilasLaConcordia' => 'SANTO DOMINGO DE LOS TSÁCHILAS:LA CONCORDIA',
            'santoDomingoDeLosTsachilasSantoDomingo' => 'SANTO DOMINGO DE LOS TSÁCHILAS:SANTO DOMINGO',
            'sucumbiosCascales' => 'SUCUMBÍOS:CASCALES',
            'sucumbiosCuyabeno' => 'SUCUMBÍOS:CUYABENO',
            'sucumbiosGonzaloPizarro' => 'SUCUMBÍOS:GONZALO PIZARRO',
            'sucumbiosLagoAgrio' => 'SUCUMBÍOS:LAGO AGRIO',
            'sucumbiosPutumayo' => 'SUCUMBÍOS:PUTUMAYO',
            'sucumbiosShushufindi' => 'SUCUMBÍOS:SHUSHUFINDI',
            'sucumbiosSucumbios' => 'SUCUMBÍOS:SUCUMBÍOS',
            'tungurahuaAmbato' => 'TUNGURAHUA:AMBATO',
            'tungurahuaBanosDeAguaSanta' => 'TUNGURAHUA:BAÑOS DE AGUA SANTA',
            'tungurahuaCevallos' => 'TUNGURAHUA:CEVALLOS',
            'tungurahuaMocha' => 'TUNGURAHUA:MOCHA',
            'tungurahuaPatate' => 'TUNGURAHUA:PATATE',
            'tungurahuaPillaro' => 'TUNGURAHUA:PÍLLARO',
            'tungurahuaQuero' => 'TUNGURAHUA:QUERO',
            'tungurahuaRioNegro' => 'TUNGURAHUA:RÍO NEGRO',
            'tungurahuaSanPedroDePelileo' => 'TUNGURAHUA:SAN PEDRO DE PELILEO',
            'tungurahuaSantiagoDePillaro' => 'TUNGURAHUA:SANTIAGO DE PÍLLARO',
            'tungurahuaTisaleo' => 'TUNGURAHUA:TISALEO',
            'zamoraChinchipeCentinelaDelCondor' => 'ZAMORA CHINCHIPE:CENTINELA DEL CÓNDOR',
            'zamoraChinchipeChinchipe' => 'ZAMORA CHINCHIPE:CHINCHIPE',
            'zamoraChinchipeElPangui' => 'ZAMORA CHINCHIPE:EL PANGUI',
            'zamoraChinchipeNangaritza' => 'ZAMORA CHINCHIPE:NANGARITZA',
            'zamoraChinchipePalanda' => 'ZAMORA CHINCHIPE:PALANDA',
            'zamoraChinchipePaquisha' => 'ZAMORA CHINCHIPE:PAQUISHA',
            'zamoraChinchipeYacuambi' => 'ZAMORA CHINCHIPE:YACUAMBI',
            'zamoraChinchipeYantzaza' => 'ZAMORA CHINCHIPE:YANTZAZA',
            'zamoraChinchipeZamora' => 'ZAMORA CHINCHIPE:ZAMORA',
        ];
    }

    protected static function labels(): array
    {
        return [
            'azuayCamiloPonceEnriquez' => 'CAMILO PONCE ENRÍQUEZ (AZUAY)',
            'azuayChordeleg' => 'CHORDELEG (AZUAY)',
            'azuayCuenca' => 'CUENCA (AZUAY)',
            'azuayElPan' => 'EL PAN (AZUAY)',
            'azuayGiron' => 'GIRÓN (AZUAY)',
            'azuayGuachapala' => 'GUACHAPALA (AZUAY)',
            'azuayGualaceo' => 'GUALACEO (AZUAY)',
            'azuayNabon' => 'NABÓN (AZUAY)',
            'azuayOna' => 'OÑA (AZUAY)',
            'azuayPaute' => 'PAUTE (AZUAY)',
            'azuayPucara' => 'PUCARÁ (AZUAY)',
            'azuaySanFernando' => 'SAN FERNANDO (AZUAY)',
            'azuaySantaIsabel' => 'SANTA ISABEL (AZUAY)',
            'azuaySevillaDeOro' => 'SEVILLA DE ORO (AZUAY)',
            'azuaySigsig' => 'SIGSIG (AZUAY)',
            'bolivarCaluma' => 'CALUMA (BOLÍVAR)',
            'bolivarChillanes' => 'CHILLANES (BOLÍVAR)',
            'bolivarChimbo' => 'CHIMBO (BOLÍVAR)',
            'bolivarEcheandia' => 'ECHEANDÍA (BOLÍVAR)',
            'bolivarGuaranda' => 'GUARANDA (BOLÍVAR)',
            'bolivarLasNaves' => 'LAS NAVES (BOLÍVAR)',
            'bolivarSanMiguel' => 'SAN MIGUEL (BOLÍVAR)',
            'canarAzogues' => 'AZOGUES (CAÑAR)',
            'canarBiblian' => 'BIBLIÁN (CAÑAR)',
            'canarCanar' => 'CAÑAR (CAÑAR)',
            'canarDeleg' => 'DÉLEG (CAÑAR)',
            'canarElTambo' => 'EL TAMBO (CAÑAR)',
            'canarLaTroncal' => 'LA TRONCAL (CAÑAR)',
            'canarSuscal' => 'SUSCAL (CAÑAR)',
            'carchiBolivar' => 'BOLÍVAR (CARCHI)',
            'carchiEspejo' => 'ESPEJO (CARCHI)',
            'carchiMira' => 'MIRA (CARCHI)',
            'carchiMontufar' => 'MONTÚFAR (CARCHI)',
            'carchiSanPedroDeHuaca' => 'SAN PEDRO DE HUACA (CARCHI)',
            'carchiTulcan' => 'TULCÁN (CARCHI)',
            'chimborazoAlausi' => 'ALAUSÍ (CHIMBORAZO)',
            'chimborazoChambo' => 'CHAMBO (CHIMBORAZO)',
            'chimborazoChunchi' => 'CHUNCHI (CHIMBORAZO)',
            'chimborazoColta' => 'COLTA (CHIMBORAZO)',
            'chimborazoCumanda' => 'CUMANDÁ (CHIMBORAZO)',
            'chimborazoGuamote' => 'GUAMOTE (CHIMBORAZO)',
            'chimborazoGuano' => 'GUANO (CHIMBORAZO)',
            'chimborazoPallatanga' => 'PALLATANGA (CHIMBORAZO)',
            'chimborazoPenipe' => 'PENIPE (CHIMBORAZO)',
            'chimborazoRiobamba' => 'RIOBAMBA (CHIMBORAZO)',
            'cotopaxiLaMana' => 'LA MANÁ (COTOPAXI)',
            'cotopaxiLasso' => 'LASSO (COTOPAXI)',
            'cotopaxiLatacunga' => 'LATACUNGA (COTOPAXI)',
            'cotopaxiMulalo' => 'MULALÓ (COTOPAXI)',
            'cotopaxiPangua' => 'PANGUA (COTOPAXI)',
            'cotopaxiPujili' => 'PUJILÍ (COTOPAXI)',
            'cotopaxiSalcedo' => 'SALCEDO (COTOPAXI)',
            'cotopaxiSaquisili' => 'SAQUISILÍ (COTOPAXI)',
            'cotopaxiSigchos' => 'SIGCHOS (COTOPAXI)',
            'cotopaxiTanicuchi' => 'TANICUCHÍ (COTOPAXI)',
            'elOroArenillas' => 'ARENILLAS (EL ORO)',
            'elOroAtahualpa' => 'ATAHUALPA (EL ORO)',
            'elOroBalsas' => 'BALSAS (EL ORO)',
            'elOroChilla' => 'CHILLA (EL ORO)',
            'elOroElGuabo' => 'EL GUABO (EL ORO)',
            'elOroHuaquillas' => 'HUAQUILLAS (EL ORO)',
            'elOroLasLajas' => 'LAS LAJAS (EL ORO)',
            'elOroMachala' => 'MACHALA (EL ORO)',
            'elOroMarcabeli' => 'MARCABELÍ (EL ORO)',
            'elOroPasaje' => 'PASAJE (EL ORO)',
            'elOroPinas' => 'PIÑAS (EL ORO)',
            'elOroPortovelo' => 'PORTOVELO (EL ORO)',
            'elOroSantaRosa' => 'SANTA ROSA (EL ORO)',
            'elOroZaruma' => 'ZARUMA (EL ORO)',
            'esmeraldasAtacames' => 'ATACAMES (ESMERALDAS)',
            'esmeraldasEloyAlfaro' => 'ELOY ALFARO (ESMERALDAS)',
            'esmeraldasEsmeraldas' => 'ESMERALDAS (ESMERALDAS)',
            'esmeraldasLaConcordia' => 'LA CONCORDIA (ESMERALDAS)',
            'esmeraldasMuisne' => 'MUISNE (ESMERALDAS)',
            'esmeraldasQuininde' => 'QUININDÉ (ESMERALDAS)',
            'esmeraldasRioverde' => 'RIOVERDE (ESMERALDAS)',
            'esmeraldasSanLorenzo' => 'SAN LORENZO (ESMERALDAS)',
            'galapagosIsabela' => 'ISABELA (GALÁPAGOS)',
            'galapagosSanCristobal' => 'SAN CRISTÓBAL (GALÁPAGOS)',
            'galapagosSantaCruz' => 'SANTA CRUZ (GALÁPAGOS)',
            'guayasAlfredoBaquerizoMorenoJujan' => 'ALFREDO BAQUERIZO MORENO (JUJAN) (GUAYAS)',
            'guayasBalao' => 'BALAO (GUAYAS)',
            'guayasBalzar' => 'BALZAR (GUAYAS)',
            'guayasColimes' => 'COLIMES (GUAYAS)',
            'guayasCoronelMarcelinoMariduena' => 'CORONEL MARCELINO MARIDUEÑA (GUAYAS)',
            'guayasDaule' => 'DAULE (GUAYAS)',
            'guayasDuran' => 'DURÁN (GUAYAS)',
            'guayasElEmpalme' => 'EL EMPALME (GUAYAS)',
            'guayasElTriunfo' => 'EL TRIUNFO (GUAYAS)',
            'guayasGeneralAntonioElizaldeBucay' => 'GENERAL ANTONIO ELIZALDE (BUCAY) (GUAYAS)',
            'guayasGuayaquil' => 'GUAYAQUIL (GUAYAS)',
            'guayasIsidroAyora' => 'ISIDRO AYORA (GUAYAS)',
            'guayasLomasDeSargentillo' => 'LOMAS DE SARGENTILLO (GUAYAS)',
            'guayasMilagro' => 'MILAGRO (GUAYAS)',
            'guayasNaranjal' => 'NARANJAL (GUAYAS)',
            'guayasNaranjito' => 'NARANJITO (GUAYAS)',
            'guayasNobol' => 'NOBOL (GUAYAS)',
            'guayasPalestina' => 'PALESTINA (GUAYAS)',
            'guayasPedroCarbo' => 'PEDRO CARBO (GUAYAS)',
            'guayasPlayas' => 'PLAYAS (GUAYAS)',
            'guayasSalitreUrbinaJado' => 'SALITRE (URBINA JADO) (GUAYAS)',
            'guayasSamborondon' => 'SAMBORONDÓN (GUAYAS)',
            'guayasSanJacintoDeYaguachi' => 'SAN JACINTO DE YAGUACHI (GUAYAS)',
            'guayasSantaLucia' => 'SANTA LUCÍA (GUAYAS)',
            'guayasSimonBolivar' => 'SIMÓN BOLÍVAR (GUAYAS)',
            'imbaburaAntonioAnte' => 'ANTONIO ANTE (IMBABURA)',
            'imbaburaCotacachi' => 'COTACACHI (IMBABURA)',
            'imbaburaIbarra' => 'IBARRA (IMBABURA)',
            'imbaburaOtavalo' => 'OTAVALO (IMBABURA)',
            'imbaburaPimampiro' => 'PIMAMPIRO (IMBABURA)',
            'imbaburaSanMiguelDeUrcuqui' => 'SAN MIGUEL DE URCUQUÍ (IMBABURA)',
            'lojaCalvas' => 'CALVAS (LOJA)',
            'lojaCariamanga' => 'CARIAMANGA (LOJA)',
            'lojaCatamayo' => 'CATAMAYO (LOJA)',
            'lojaCelica' => 'CELICA (LOJA)',
            'lojaChaguarpamba' => 'CHAGUARPAMBA (LOJA)',
            'lojaEspindola' => 'ESPÍNDOLA (LOJA)',
            'lojaGonzanama' => 'GONZANAMÁ (LOJA)',
            'lojaLoja' => 'LOJA (LOJA)',
            'lojaMacara' => 'MACARÁ (LOJA)',
            'lojaOlmedo' => 'OLMEDO (LOJA)',
            'lojaPaltas' => 'PALTAS (LOJA)',
            'lojaPindal' => 'PINDAL (LOJA)',
            'lojaPuyango' => 'PUYANGO (LOJA)',
            'lojaQuilanga' => 'QUILANGA (LOJA)',
            'lojaSaraguro' => 'SARAGURO (LOJA)',
            'lojaSozoranga' => 'SOZORANGA (LOJA)',
            'lojaZapotillo' => 'ZAPOTILLO (LOJA)',
            'losRiosBaba' => 'BABA (LOS RÍOS)',
            'losRiosBabahoyo' => 'BABAHOYO (LOS RÍOS)',
            'losRiosBuenaFe' => 'BUENA FE (LOS RÍOS)',
            'losRiosMocache' => 'MOCACHE (LOS RÍOS)',
            'losRiosMontalvo' => 'MONTALVO (LOS RÍOS)',
            'losRiosPalenque' => 'PALENQUE (LOS RÍOS)',
            'losRiosPuebloviejo' => 'PUEBLOVIEJO (LOS RÍOS)',
            'losRiosQuevedo' => 'QUEVEDO (LOS RÍOS)',
            'losRiosQuinsaloma' => 'QUINSALOMA (LOS RÍOS)',
            'losRiosUrdaneta' => 'URDANETA (LOS RÍOS)',
            'losRiosValencia' => 'VALENCIA (LOS RÍOS)',
            'losRiosVentanas' => 'VENTANAS (LOS RÍOS)',
            'losRiosVinces' => 'VINCES (LOS RÍOS)',
            'manabi24DeMayo' => '24 DE MAYO (MANABÍ)',
            'manabiBolivar' => 'BOLÍVAR (MANABÍ)',
            'manabiChone' => 'CHONE (MANABÍ)',
            'manabiElCarmen' => 'EL CARMEN (MANABÍ)',
            'manabiFlavioAlfaro' => 'FLAVIO ALFARO (MANABÍ)',
            'manabiJama' => 'JAMA (MANABÍ)',
            'manabiJaramijo' => 'JARAMIJÓ (MANABÍ)',
            'manabiJipijapa' => 'JIPIJAPA (MANABÍ)',
            'manabiJunin' => 'JUNÍN (MANABÍ)',
            'manabiManta' => 'MANTA (MANABÍ)',
            'manabiMontecristi' => 'MONTECRISTI (MANABÍ)',
            'manabiOlmedo' => 'OLMEDO (MANABÍ)',
            'manabiPajan' => 'PAJÁN (MANABÍ)',
            'manabiPedernales' => 'PEDERNALES (MANABÍ)',
            'manabiPichincha' => 'PICHINCHA (MANABÍ)',
            'manabiPortoviejo' => 'PORTOVIEJO (MANABÍ)',
            'manabiPuertoLopez' => 'PUERTO LÓPEZ (MANABÍ)',
            'manabiRocafuerte' => 'ROCAFUERTE (MANABÍ)',
            'manabiSanVicente' => 'SAN VICENTE (MANABÍ)',
            'manabiSantaAna' => 'SANTA ANA (MANABÍ)',
            'manabiSucre' => 'SUCRE (MANABÍ)',
            'manabiTosagua' => 'TOSAGUA (MANABÍ)',
            'moronaSantiagoGualaquiza' => 'GUALAQUIZA (MORONA SANTIAGO)',
            'moronaSantiagoHuamboya' => 'HUAMBOYA (MORONA SANTIAGO)',
            'moronaSantiagoLimonIndanza' => 'LIMÓN INDANZA (MORONA SANTIAGO)',
            'moronaSantiagoLogrono' => 'LOGROÑO (MORONA SANTIAGO)',
            'moronaSantiagoMacas' => 'MACAS (MORONA SANTIAGO)',
            'moronaSantiagoMorona' => 'MORONA (MORONA SANTIAGO)',
            'moronaSantiagoPabloSexto' => 'PABLO SEXTO (MORONA SANTIAGO)',
            'moronaSantiagoPalora' => 'PALORA (MORONA SANTIAGO)',
            'moronaSantiagoSanJuanBosco' => 'SAN JUAN BOSCO (MORONA SANTIAGO)',
            'moronaSantiagoSantiago' => 'SANTIAGO (MORONA SANTIAGO)',
            'moronaSantiagoSucua' => 'SUCÚA (MORONA SANTIAGO)',
            'moronaSantiagoTaisha' => 'TAISHA (MORONA SANTIAGO)',
            'moronaSantiagoTiwintza' => 'TIWINTZA (MORONA SANTIAGO)',
            'napoArchidona' => 'ARCHIDONA (NAPO)',
            'napoCarlosJulioArosemenaTola' => 'CARLOS JULIO AROSEMENA TOLA (NAPO)',
            'napoElChaco' => 'EL CHACO (NAPO)',
            'napoQuijos' => 'QUIJOS (NAPO)',
            'napoTena' => 'TENA (NAPO)',
            'orellanaAguarico' => 'AGUARICO (ORELLANA)',
            'orellanaElCoca' => 'EL COCA (ORELLANA)',
            'orellanaLaJoyaDeLosSachas' => 'LA JOYA DE LOS SACHAS (ORELLANA)',
            'orellanaLoreto' => 'LORETO (ORELLANA)',
            'orellanaOrellana' => 'ORELLANA (ORELLANA)',
            'pastazaArajuno' => 'ARAJUNO (PASTAZA)',
            'pastazaMera' => 'MERA (PASTAZA)',
            'pastazaPastaza' => 'PASTAZA (PASTAZA)',
            'pastazaPuyo' => 'PUYO (PASTAZA)',
            'pastazaSantaClara' => 'SANTA CLARA (PASTAZA)',
            'pastazaShell' => 'SHELL (PASTAZA)',
            'pichinchaCayambe' => 'CAYAMBE (PICHINCHA)',
            'pichinchaMejia' => 'MEJÍA (PICHINCHA)',
            'pichinchaPedroMoncayo' => 'PEDRO MONCAYO (PICHINCHA)',
            'pichinchaPedroVicenteMaldonado' => 'PEDRO VICENTE MALDONADO (PICHINCHA)',
            'pichinchaPuertoQuito' => 'PUERTO QUITO (PICHINCHA)',
            'pichinchaQuito' => 'QUITO (PICHINCHA)',
            'pichinchaRuminahui' => 'RUMIÑAHUI (PICHINCHA)',
            'pichinchaSanMiguelDeLosBancos' => 'SAN MIGUEL DE LOS BANCOS (PICHINCHA)',
            'pichinchaMachachi' => 'MACHACHI (PICHINCHA)',
            'pichinchaSangolqui' => 'SANGOLQUÍ (PICHINCHA)',
            'santaElenaLaLibertad' => 'LA LIBERTAD (SANTA ELENA)',
            'santaElenaSalinas' => 'SALINAS (SANTA ELENA)',
            'santaElenaSantaElena' => 'SANTA ELENA (SANTA ELENA)',
            'santoDomingoDeLosTsachilasLaConcordia' => 'LA CONCORDIA (SANTO DOMINGO DE LOS TSÁCHILAS)',
            'santoDomingoDeLosTsachilasSantoDomingo' => 'SANTO DOMINGO (SANTO DOMINGO DE LOS TSÁCHILAS)',
            'sucumbiosCascales' => 'CASCALES (SUCUMBÍOS)',
            'sucumbiosCuyabeno' => 'CUYABENO (SUCUMBÍOS)',
            'sucumbiosGonzaloPizarro' => 'GONZALO PIZARRO (SUCUMBÍOS)',
            'sucumbiosLagoAgrio' => 'LAGO AGRIO (SUCUMBÍOS)',
            'sucumbiosPutumayo' => 'PUTUMAYO (SUCUMBÍOS)',
            'sucumbiosShushufindi' => 'SHUSHUFINDI (SUCUMBÍOS)',
            'sucumbiosSucumbios' => 'SUCUMBÍOS (SUCUMBÍOS)',
            'tungurahuaAmbato' => 'AMBATO (TUNGURAHUA)',
            'tungurahuaBanosDeAguaSanta' => 'BAÑOS DE AGUA SANTA (TUNGURAHUA)',
            'tungurahuaCevallos' => 'CEVALLOS (TUNGURAHUA)',
            'tungurahuaMocha' => 'MOCHA (TUNGURAHUA)',
            'tungurahuaPatate' => 'PATATE (TUNGURAHUA)',
            'tungurahuaPillaro' => 'PÍLLARO (TUNGURAHUA)',
            'tungurahuaQuero' => 'QUERO (TUNGURAHUA)',
            'tungurahuaRioNegro' => 'RÍO NEGRO (TUNGURAHUA)',
            'tungurahuaSanPedroDePelileo' => 'SAN PEDRO DE PELILEO (TUNGURAHUA)',
            'tungurahuaSantiagoDePillaro' => 'SANTIAGO DE PÍLLARO (TUNGURAHUA)',
            'tungurahuaTisaleo' => 'TISALEO (TUNGURAHUA)',
            'zamoraChinchipeCentinelaDelCondor' => 'CENTINELA DEL CÓNDOR (ZAMORA CHINCHIPE)',
            'zamoraChinchipeChinchipe' => 'CHINCHIPE (ZAMORA CHINCHIPE)',
            'zamoraChinchipeElPangui' => 'EL PANGUI (ZAMORA CHINCHIPE)',
            'zamoraChinchipeNangaritza' => 'NANGARITZA (ZAMORA CHINCHIPE)',
            'zamoraChinchipePalanda' => 'PALANDA (ZAMORA CHINCHIPE)',
            'zamoraChinchipePaquisha' => 'PAQUISHA (ZAMORA CHINCHIPE)',
            'zamoraChinchipeYacuambi' => 'YACUAMBI (ZAMORA CHINCHIPE)',
            'zamoraChinchipeYantzaza' => 'YANTZAZA (ZAMORA CHINCHIPE)',
            'zamoraChinchipeZamora' => 'ZAMORA (ZAMORA CHINCHIPE)',
        ];
    }
}
