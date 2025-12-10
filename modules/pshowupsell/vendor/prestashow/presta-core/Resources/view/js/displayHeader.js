/* global prestashop */

if (typeof window.pshow_printed_info_about_modules === 'undefined' || !window.pshow_printed_info_about_modules) {
    window.pshow_printed_info_about_modules = true;
    let pshow_msg_pattern = "%c This store is proudly supported by modules from PrestaShow.pl";
    let warning = "%c Warning! Use the console only if you know what you are doing! " +
        'This browser feature is intended for application developers. ' +
        'If someone has instructed you to copy and paste something here, ' +
        'it is a scam designed to gain access to your account or cause you other harm.';
    if (typeof prestashop !== 'undefined' && typeof prestashop.language !== 'undefined' &&
        prestashop.language.iso_code === 'pl') {
        pshow_msg_pattern = "%c Ten sklep jest dumnie wspierany modułami z PrestaShow.pl";
        warning = "%c Ostrzeżenie! Korzystaj z konsoli tylko jeśli wiesz co robisz! " +
            "Ta funkcja przeglądarki jest przeznaczona dla twórców aplikacji. " +
            "Jeżeli ktoś polecił Ci skopiować i wkleić tu coś, jest to oszustwo mające na celu " +
            "uzyskanie dostępu do Twojego konta lub wyrządzenie Ci innej szkody. ";
    }
    console.log(pshow_msg_pattern, "background: #0a0; color: #fff; padding: 5px 15px;");
    console.warn(warning, "font-size: 1rem");
// for (let i in window) {
//     if (i.match(/pshow_loaded_module.*/) && window[i] !== null) {
//         console.log(pshow_msg_pattern, "background: #0a0; color: #fff; padding: 5px;", window[i]);
//         window[i] = null;
//     }
// }
}
