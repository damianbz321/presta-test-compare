/**
* 2014-2021 Presta-Mod.pl Rafał Zontek
*
* NOTICE OF LICENSE
*
* Poniższy kod jest kodem płatnym, rozpowszechanie bez pisemnej zgody autora zabronione
* Moduł można zakupić na stronie Presta-Mod.pl. Modyfikacja kodu jest zabroniona,
* wszelkie modyfikacje powodują utratę gwarancji
*
* http://presta-mod.pl
*
* DISCLAIMER
*
*
*  @author    Presta-Mod.pl Rafał Zontek <biuro@presta-mod.pl>
*  @copyright 2014-2021 Presta-Mod.pl
*  @license   Licecnja na jedną domenę
*  Presta-Mod.pl Rafał Zontek
*/
$(document).ready(function(){    
    //simple config. 
    //Only one step - highlighting(with description) "New" button 
    //hide EnjoyHint after a click on the button.
    var enjoyhint_instance = false;
    var current_step = 0;

  function changeCurrentStep() {
        current_step = enjoyhint_instance.getCurrentStep();
        $('input[name="PMINPOSTPACZKOMATY_GUIDE"]').val(current_step);
        $('.pminpostpaczkomaty-guide-step').text((current_step+1).toString() + ' / ' + enjoyhint_script_steps.length.toString());
    }

    //set script config
    function resumePmGuide() {
        enjoyhint_instance = new EnjoyHint({
            onNext: changeCurrentStep,                
            onSkip: changeCurrentStep,                
            btnNextText: 'Dalej',
            btnSkipText: 'Ukryj'
        });
        enjoyhint_instance.set(enjoyhint_script_steps);
        enjoyhint_instance.setCurrentStep(current_step);
        enjoyhint_instance.resume();
    }

    if ($('input[name="PMINPOSTPACZKOMATY_GUIDE"]').length && $('input[name="PMINPOSTPACZKOMATY_GUIDE"]').val()) {
        current_step = parseInt($('input[name="PMINPOSTPACZKOMATY_GUIDE"]').val());
    }
    var page  = 1;
    enjoyhint_script_steps = [
        {
            selector:'.start-guide',
            shape: 'circle',
            description: `
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Witaj w przewodniku konfiguracji modułu Paczkomaty InPost<br/> 
                    Dostęp do samouczka jest tutaj
                </p>
            `,            
        },
        {
            selector:'.pminpostpaczkomaty #refreshcategories',
            shape: 'circle',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Jeśli samouczek nie wystarczy skorzystaj z Instrukcji.
                </p>
            `,            
        },
        {
            selector:'.pminpostpaczkomaty .sidebar',
            shape: 'circle',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Główne zakładki konfiguracji, najważniejsze zaznaczyliśmy pogrubioną czcionką<br/>
                    Samouczek dotyczący konfiguracji to 31 kroków - czy to dużo?<br/>
                    Konfiguracja zajmie kilka minut. Z pomocą samouczka na pewno pójdzie gładko.
                </p>
            `,            
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(0)"]',
            shape: 'circle',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Przejdźmy do konfiguracji połączenia z InPost
                </p>
            `,
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(0)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(0)',
            description: `
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Jeżeli chcesz możesz użyć starego API - autoryzacja za pomocą adresu e-mail oraz hasła z manager.paczkomaty.pl<br/>
                    Możesz również skorzystać z API SHIPX (nowe, API) - autoryzacja za pomocą Tokenu i Id organizacji. <br/>Możesz je uzyskać w manager.paczkomaty.pl &rArr; Moje konto &rArr; API
                </p>
            `,                        
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(0)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty button[name="saveAndTest"]:eq(0)',
            shape: 'circle',
            description: `
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Po wprowadzeniu danych spróbuj się połączyć z API
                </p>
            `,                        
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(0)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(1)"]',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Wprowadzamy dane nadawcy
                </p>
            `,            
            shape: 'circle',            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(1)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(1)',
            description: `
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Uzupełnij wszystkie poniższe pola. Jeśli w nazwie twojej firmy nie występuje imię i nazwisko wprowadź dane osoby kontaktowej z InPost. <br/>
                    Numer telefonu - musi to być telefon komórkowy - bez prefiksu (+48)<br/>
                    Korzystasz z tokena ShipX? Niektóre dane pobraliśmy z Twojego konta w InPost.
                </p>
            `,                        
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(1)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(2)"]',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Konfiguracja przewoźnika - dla przesyłek opłaconych z góry (Przelew, Płatności elektroniczne)
                </p>
            `,            
            shape: 'circle',            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(2)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(2)',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Wybierz metodę dostawy dla paczkomatów - jeżeli metoda dostawy była wcześniej utworzona, a jej tu nie widzisz - oznacza, że jest powiązana z innym modułem. <br/>
                    Skorzystaj z przycisku Utwórz metodę dostawy - ta opcja przekieruje do tworzenia Nowego przewoźnika - Nie zapomnij uzupełnić cen<br/>
                    Domyślny rozmiar dla przesyłek - określ jaki rozmiar odpowiada najbardziej twoim wysyłką - nie martw się możesz go później zmienić podczas generowania etykiety.<br/>                    
                    <i>Użyj CTRL aby zaznaczyć kilka pozycji</i>
                </p>
            `,
                    
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(2)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(3)"]',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">Konfiguracja przewoźnika - dla przesyłek za pobraniem</p>
            `,            
            shape: 'circle',            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(3)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(3)',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Ten krok można całkowicie pominąć - nie chcę wysyłać przesyłek za pobraniem.<br/><br/>
                    Konfiguracja jest podobna do Przesyłek opłaconych z góry. <br/>
                    <i>Jeżeli wybierzesz tą samą metodę dostawy jak w przypadku przesyłek opłaconych z góry - przy każdym zamówieniu moduł zaznaczy Pobranie: Tak,<br/>aby tego uniknąć zaleczamy utworzenie osobnej metody dostawy dla InPost paczkomaty pobranie.</i>
                </p>
            `,
                    
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(3)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(4)"]',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">Ustawienia przesyłki</p>
            `,            
            shape: 'circle',            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(4)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(4)',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    W tym punkcie wybierasz sposób nadania, oraz typ drukowanej etykiety.<br/>
                    Możesz na etykietach automatycznie dodawać nr zamówienia, lub imię i nazwisko klienta za pomocą kodu referencyjnego
                </p>
            `,
                    
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(4)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(5)"]',
            description:`
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Ustawienia mapy
                </p>
            `,            
            shape: 'circle',            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(5)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(5)',
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Wybór paczkomatu z listy - udostępni twoim klientom prosty wybór paczkomatu.<br/>
                    Zalecamy używać darmowego OpenMaps. <br/>
                    Geolokalizacja działa gdy masz włączony SSL (zielona kłódka na pasku przeglądarki).
                </p>
            `,
                    
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(5)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(6)"]',
            description: `
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Ustawienia położenia
                </p>
            `,            
            shape: 'circle',
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(6)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(6)',
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Jeżeli poprawnie skonfigurowałeś moduł oraz metodę dostawy możesz spróbować złożyć testowe zamówienie.
                    W tym kroku możesz wybrać które położenie jest najlepsze dla twojego sklepu.
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(6)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(7)"]',
            description: `
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Ustawianie statusów zamówienia
                </p>
            `,            
            shape: 'circle',
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(7)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(7)',
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Chcesz klienta informować o wysłanych paczkach? <br/>
                    Możesz automatycznie oznaczać przesyłki odebrane przez kuriera, dodatkowo sam będziesz wiedział czy klient odebrał przesyłkę.
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(7)"]').click();
                return true;
            },
        },
        {
            selector: $('#PMINPOSTPACZKOMATY_OS_on').parent().parent().parent(),
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Chcesz zmienić status zamówienia po wydrukowaniu listu? Nic prostszego - zaznacz Tak
                    Następnie ustaw Status zamówienia po wysłaniu
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(7)"]').click();
                return true;
            },
        },
        {
            selector: $('#PMINPOSTPACZKOMATY_STATUS').parent().parent(),
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Wybierz jaki status mają mieć przesyłki po wysłaniu
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(7)"]').click();
                return true;
            },
        },
        {
            selector: $('select[name="PMINPOSTPACZKOMATY_STATUS_AV[]"').parent().parent(),
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Możesz wybrać które statusy mają być aktualizowane - jest opcja dla zaawansowanych użytkowników - śmiało możesz zaznaczyć wszystkie <i>Ctrl + A</i><br/>
                    <i>Użyj CTRL aby zaznaczyć kilka pozycji</i>
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(7)"]').click();
                return true;
            },
        },
        {
            selector: $('#PMINPOSTPACZKOMATY_STATUS_PIC').parent().parent(),
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Jeżeli chcesz możesz zmienić status tuż po odebraniu przesyłki przez kuriera / nadaniu w paczkomacie - wybierz status
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(7)"]').click();
                return true;
            },
        },
        {
            selector: $('#PMINPOSTPACZKOMATY_STATUS_DEL').parent().parent(),
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Wybierz status dla przesyłek dostarczonych<br/>
                    Nie zapomnij dodać zadania cron.
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(7)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(8)"]',
            description: `
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Ustawianie wiadomości do klienta
                </p>
            `,            
            shape: 'circle',
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(8)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(8)',
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Możesz wysłać wiadomość do klienta o wybranym paczkomacie<br/>
                    Wyślij dodatkową wiadomość zaznacz na Tak <br/>
                    W <strong>Wiadomość do klienta</strong> - użyj zmiennych aby ją poprawnie sformatować.
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(8)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(9)"]',
            description: `
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Ustawianie wiadomości do zamówienia
                </p>
            `,            
            shape: 'circle',
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(9)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(9)',
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Możesz skorzystać z tej opcji, automatycznie do zamówienia doda się informacja o wybranym paczkomacie, dzięki tej opcji można pobrać informacje o paczkomacie w API PrestaShop
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(9)"]').click();
                return true;
            },
        },
        {
            selector:'.list-group-item[data-target=".panel:eq(12)"]',
            description: `
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Integracja API Baselinkera
                </p>
            `,            
            shape: 'circle',
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(12)"]').click();
                return true;
            },
        },
        {
            selector:'.pminpostpaczkomaty .panel:eq(12)',
            description:`                
                <span class="pmstep">`+(page++)+` / {total}</span><p class="pmguide-description">
                    Korzystasz z Baselinkera? Możesz przesyłać informacje o wybranych paczkomatach poprzez API baselinkera.<br>
                    Baselinker może być podłączony poprzez konektor w postaci pliku PHP - wtedy automatycznie przesyłki powinny trafiać do panelu baselinker, jeśli opcja nie działa spróbuj zaktualizować konektor, lub skonfiguruj Integracja API Baselinker.<br/>
                    W przypadku połączenia poprzez API PrestaShop - skonfiguruj Integracja API Baselinker
                </p>
            `,            
            onBeforeStart: function(){
                $('a[data-target=".panel:eq(12)"]').click();
                return true;
            },
        },
        
    ];
    for(i in enjoyhint_script_steps) {
        enjoyhint_script_steps[i].description = enjoyhint_script_steps[i].description.replace('{total}',enjoyhint_script_steps.length);
        enjoyhint_script_steps[i]['nextButton'] = {text: "Dalej"};
        enjoyhint_script_steps[i]['skipButton'] = {text: "Ukryj"};
        if (i) {
            enjoyhint_script_steps[i]['prevButton'] = {text: "Wstecz"};
        }
        if (i == enjoyhint_script_steps.length-1) {
            enjoyhint_script_steps[i]['showNext'] = false;
        } else {
            enjoyhint_script_steps[i]['showNext'] = true;
        }
    }
  
    $(document).on('click', '.start-guide', function(){
        resumePmGuide()        
        return false;
    })
    $('.pminpostpaczkomaty-guide-step').text((parseInt(current_step)+1).toString() + ' / ' + enjoyhint_script_steps.length.toString());
});

