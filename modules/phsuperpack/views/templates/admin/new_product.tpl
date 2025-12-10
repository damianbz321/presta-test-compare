<div class="modal-content">
    <div class="control-label col-lg-8 mb-2">
        <label class="">{l s='Set title' mod='phsuperpack'}</label>
        <input type="text" id="pack_name" class="form-control">
    </div>

    <div class="control-label col-lg-8 mb-4">
        <label class="">{l s='Product search' mod='phsuperpack'}</label>
        <input type="text" id="product_search" class="form-control">
        
        <div id="founded_product_list" style="display: none;">
            <ul></ul>
        </div>
        
        <small>Wpisz minimum 3 znaki, aby wyszukać produkty.</small>
    </div>

    <div class="control-label col-lg-8 mb-2">
        <table class="table">
            <thead>
                <th>{l s='Image' mod='phsuperpack'}</th>
                <th>{l s='Product name' mod='phsuperpack'}</th>
                <th>{l s='Qauntity' mod='phsuperpack'}</th>
                <th>{l s='Action' mod='phsuperpack'}</th>
            </thead>
            <tbody id="choosedProducts">
            </tbody>
        </table>

        <input type="hidden" id="selected_products">
    </div>

    <div class="control-label col-lg-8 mb-2">
        <input type="checkbox" id="for_all_products">
        <label class="" for="for_all_products">{l s='Dodaj zestaw dla wszystkich produktów z zestawu' mod='phsuperpack'}</label>
    </div>

    <div class="control-label col-lg-8 mb-2">
        <input type="checkbox" id="for_all_combination">
        <label class="" for="for_all_combination">{l s='Dodaj zestaw dla wszystkich kombinacji produktów' mod='phsuperpack'}</label>
    </div>

    <div class="control-label col-lg-8 mb-2">
        <p id="without_reduction" style="color: red; display: none;">Suma: <span id="without_reduction_nett_price">{$basic_price_nett}</span> netto, <span id="without_reduction_gross_price">{$basic_price}</span> brutto</p>
    </div>

    <div class="control-label col-lg-8 mb-2">
        <label class="">{l s='Price for whole pack' mod='phsuperpack'}</label>
        <div class="input-group money-type">
            <input type="text" id="pack_price" disabled name="pack_price" class="form-control" value="{$basic_price}" autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text">{$currency->sign}</span>
            </div>
        </div>
    </div>

    <div class="row" style="margin-left: 0;">
        <div class="col-xl-2 col-lg-3">
            <fieldset class="form-group">
            <label>Zastosuj zniżkę</label>					
            <div class="input-group money-type">
                <input type="text" id="pack_products_reduction" name="pack_products_reduction" class="form-control" autocomplete="off" value="0,000000">
                <div class="input-group-append">
                    <span class="input-group-text" id="reduction_type_sign">{$currency->sign}</span>
                </div>
                </div>
            </fieldset>
        </div>
        <div class="col-xl-2 col-lg-3">
            <fieldset class="form-group">
            <label>&nbsp;</label>					
                <select id="pack_products_reduction_type" name="pack_products_reduction_type" class="custom-select">
                    <option value="amount" data-sign="{$currency->sign}">{$currency->sign}</option>
                    <option value="percentage" data-sign="%">%</option>
                </select>
            </fieldset>
        </div>
    </div>

    <div class="control-label col-lg-8 mb-2">
        <p id="full_price" style="color: red; display: none;">Suma: <span id="nett_price">{$basic_price_nett}</span> netto, <span id="gross_price">{$basic_price}</span> brutto</p>
    </div>

    <div class="control-label col-lg-8 mb-2">
        <label for="packImage">Wybierz okładkę</label>
        <input type="file" id="packImage" class="form-control">
    </div>

    <input type="hidden" id="activePack" value="1">

    <div style="display: flex; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="clearFileds();" class="btn btn-action">Wyczyść</button>
        <button type="button" id="createPack" class="btn btn-primary">Zapisz</button>
    </div>
</div>

<div style="margin: 20px 0px;">
    <table class="table">
        <thead>
            <th>#</th>
            <th>{l s='Product name' mod='phsuperpack'}</th>
            <th>{l s='Nett price' mod='phsuperpack'}</th>
            <th>{l s='Gross price' mod='phsuperpack'}</th>
            <th>{l s='Price after discount nett' mod='phsuperpack'}</th>
            <th>{l s='Price after discount gross' mod='phsuperpack'}</th>
            <th>{l s='Qauntity' mod='phsuperpack'}</th>
            <th>{l s='Active' mod='phsuperpack'}</th>
            <th>{l s='Action' mod='phsuperpack'}</th>
            <th>{l s='Position' mod='phsuperpack'}</th>
        </thead>
        <tbody id="created_packs">
            {foreach from=$packs item=pack}
                <tr>
                    <td>{$pack.id}</td>
                    <td><b>{$pack.name}</b> {$pack.products_names}</td>
                    <td>{$pack.price_nett}</td>
                    <td>{$pack.price_gross}</td>
                    <td>{$pack.discount_nett}</td>
                    <td>{$pack.discount_gross}</td>
                    <td>{$pack.quantity}</td>
                    <td>
                        <input type="checkbox" class="pack_active" onchange="changeActive(this);" value="{$pack.id}" {if $pack.active == 1}checked{/if}>
                    </td>
                    <td>
                        <button class="btn btn-action editPack" onclick="editPack(this);" type="button" data-id="{$pack.id}" data-active="{$pack.active}">Edytuj</button>
                        <button class="btn btn-action deletePack" onclick="deletePack(this);" type="button" data-id="{$pack.id}">Usuń</button>
                    </td>
                    <td>
                        <button class="btn btn-action" onclick="changePosition(this);" type="button" data-id="{$pack.id}" what-to-do="upper" data-position="{$pack.position}">↑</button>
                        <button class="btn btn-action" onclick="changePosition(this);" type="button" data-id="{$pack.id}" what-to-do="lower" data-position="{$pack.position}">↓</button>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
</div>

{literal}
<script>

    function changePriceWithoutReduction() {
        const main_price = document.querySelector("#pack_price");
        
        const nett_price = document.querySelector("#without_reduction_nett_price");
        const gross_price = document.querySelector("#without_reduction_gross_price");

        nett_price.textContent = parseFloat(main_price.value / {/literal}{$tax_rate}{literal}).toFixed(2);
        gross_price.textContent = parseFloat(main_price.value).toFixed(2);
    }

    function changePrice(value, type) {
        const nett_price = document.querySelector("#nett_price");
        const gross_price = document.querySelector("#gross_price");
        const main_price = document.querySelector("#pack_price").value;

        if(value != '' && main_price != '') {
            value = parseFloat(value);

            if(type == 'amount') {
                nett_price.textContent = parseFloat((main_price / {/literal}{$tax_rate}{literal}) - value).toFixed(2);
                gross_price.textContent = parseFloat(main_price - (value * {/literal}{$tax_rate}{literal})).toFixed(2);
            }
            else {
                nett_price.textContent = parseFloat((main_price / {/literal}{$tax_rate}{literal}) - (main_price * (value / 100))).toFixed(2);
                gross_price.textContent = parseFloat(main_price - (main_price * ((value * {/literal}{$tax_rate}{literal}) / 100))).toFixed(2);
            }
        }
        else {
            nett_price.textContent = parseFloat(main_price / {/literal}{$tax_rate}{literal}).toFixed(2);
            gross_price.textContent = parseFloat(main_price).toFixed(2);
        }

        changePriceWithoutReduction();
    }

    function updateEndPriceByInput() {
        const input = document.querySelector("#pack_products_reduction");
        
        input.addEventListener('change', function() {
            const type = document.querySelector("#pack_products_reduction_type");
            changePrice(input.value, type.value);
        });
    }

    function updateEndPriceByType() {
        const type = document.querySelector("#pack_products_reduction_type");

        type.addEventListener('change', function() {
            const input = document.querySelector("#pack_products_reduction");
            changePrice(input.value, type.value);
        });
    }

    function searchProduct() {
        const input = document.querySelector("#product_search");
        input.addEventListener('input', function() {
            if (input.value.length >= 3) {
                $.ajax({
                    url: '{/literal}{$search_product}{literal}&search=' + encodeURIComponent(input.value),
                    type: 'GET',
                    success: function(response) {
                        const foundedProductsList = document.querySelector("#founded_product_list > ul");
                        foundedProductsList.innerHTML = ''; // Clear the list before appending new items

                        if (response.length > 0) {
                            document.querySelector("#founded_product_list").style.display = "block";

                            response.forEach(product => {
                                const li = document.createElement("li");
                                li.innerHTML = product.name + ', (Ilość: ' + product.quantity + '), (Cena: ' + product.price + ')';
                                li.setAttribute('data-product', product.id_product);

                                // Corrected onclick to use an anonymous function
                                li.onclick = function() {
                                    addIntoList(li);
                                };

                                foundedProductsList.appendChild(li);
                            });
                        } else {
                            document.querySelector("#founded_product_list").style.display = "none";
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Wystąpił błąd:', status, error);
                    }
                });
            }
        });
    }

    function createRecordInTable(id_product, product) {
        const table = document.querySelector("#choosedProducts");
        const tr = document.createElement("tr");
        tr.setAttribute("data-id", id_product);

        // Create the first cell <td> with image
        const td1 = document.createElement("td");
        const img = document.createElement("img");
        img.src = product.image;
        img.style.width = "50px";
        td1.appendChild(img);

        // Create the second cell <td> with product name
        const td2 = document.createElement("td");
        td2.textContent = product.name;

        let quantity = 1;
        if(product.choosed_quantity) {
            quantity = product.choosed_quantity;
        }

        // Create the third cell <td> with input
        const td3 = document.createElement("td");
        const input = document.createElement("input");
        input.type = "number";
        input.className = "products_quantity";
        input.setAttribute("data-id", id_product);
        input.setAttribute("data-price", product.basic_price);
        input.value = quantity;
        input.style = "width: 90px;";
        input.onchange = function() {
            changeQuantityPrice(id_product, product.basic_price, product.quantity);
        }
        td3.appendChild(input);

        // Add text after the input ("/" + quantity)
        const span = document.createElement("span");
        span.textContent = "/" + product.quantity;
        td3.appendChild(span);

        // Create the fourth cell <td> (empty cell)
        const td4 = document.createElement("td");
        const deleteProductButton = document.createElement("a");
        deleteProductButton.innerHTML = 'Usuń';
        deleteProductButton.onclick = function() {
            deleteProduct(id_product, product.basic_price);
        }
        td4.appendChild(deleteProductButton);

        // Add the cells <td> to the row <tr>
        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);
        tr.appendChild(td4);

        // Add the row <tr> to the table
        table.appendChild(tr);
    }

    function checkExistInList(id_product) {
        const products = document.querySelectorAll("#choosedProducts > tr");
        let exist = false;
        products.forEach(product => {
            if(parseInt(product.getAttribute('data-id')) == id_product) {
                exist = true;
            }
        });

        return exist;
    }

    function addIntoList(product) {
        document.querySelector("#without_reduction").style = "display: block; color: red;";
        document.querySelector("#full_price").style = "display: block; color: red;";

        if(checkExistInList(product.getAttribute('data-product')) == true) {
            alert("Produkt juz istnieje w liście.");
            return;
        }
        document.querySelector("#founded_product_list").style = "display: none;";
        
        const id_product = product.getAttribute('data-product');
        /*
        let selected_products = document.querySelector("#selected_products");

        if (selected_products.value == '') {
            selected_products.value = id_product;
        } else {
            let tmp = selected_products.value.split(',');
            if (!tmp.includes(id_product)) {
                tmp.push(id_product);
            }
            selected_products.value = tmp.join(',');
        }
        */

        $.ajax({
            url: '{/literal}{$get_product_data}{literal}&id_product=' + id_product, // Corrected to use product id
            type: 'GET',
            success: function(response) {
                // create record in table
                createRecordInTable(id_product, response);
                
                // Update the total price
                const packPriceInput = document.querySelector("#pack_price");
                packPriceInput.value = (parseFloat(packPriceInput.value) + parseFloat(response.basic_price)).toFixed(2);
                
                changePrice('', '');
            },
            error: function(xhr, status, error) {
                console.error('Wystąpił błąd:', status, error);
            }
        });
    }

    function deleteProduct(id_product, price) {
        const product = document.querySelector('tr[data-id="'+id_product+'"]');
        if (product) {
            product.remove();

            // Update the total price
            const priceInput = document.querySelector("#pack_price");
            priceInput.value = (parseFloat(priceInput.value) - parseFloat(price)).toFixed(2);
        }
    }

    function changeQuantityPrice(id_product, price, whole_quantity) {
        // Update the total price
        const priceInput = document.querySelector("#pack_price");
        const input = document.querySelector(".products_quantity[data-id='"+id_product+"']");

        if(input.value == '') {
            input.value = whole_quantity;
        }

        let tmp_price = parseFloat(priceInput.value) - parseFloat(input.getAttribute("data-price"));
        priceInput.value = (parseFloat(tmp_price) + (parseFloat(price) * parseInt(input.value))).toFixed(2);
    
        input.setAttribute('data-price', (parseFloat(price) * parseInt(input.value)));

        changePrice(document.querySelector("#pack_products_reduction").value, document.querySelector("#pack_products_reduction_type").value);
    }

    function changeTypeRedution() {
        const select = document.querySelector("#pack_products_reduction_type");
        const tpl = document.querySelector("#reduction_type_sign");

        select.addEventListener('change', function() {
            tpl.innerHTML = select.options[select.selectedIndex].getAttribute("data-sign");
        });
    }

    function getProducts() {
        const products = document.querySelectorAll("#choosedProducts > tr");
        let products_data = [];

        products.forEach(product => {
            let id_product = product.getAttribute('data-id');

            let tmp = {
                'id_product': id_product,
                'quantity': product.querySelector(".products_quantity[data-id='"+id_product+"']").value,
            };

            products_data.push(tmp);
        });

        return products_data;
    }

    function updateList() {
        const table = document.querySelector("#created_packs");

        $.ajax({
            url: '{/literal}{$fetch_packs}{literal}&id_product={/literal}{$id_product}{literal}',
            type: 'GET',
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.packs) {
                    table.innerHTML = '';
                    const packs = response.packs;
                    for(let i=0; i<packs.length; i++) {
                        const tr = document.createElement("tr");
                        
                        // id pack
                        const td_id = document.createElement("td");
                        td_id.innerHTML = packs[i].id;

                        // name
                        const td_name = document.createElement("td");
                        td_name.innerHTML = '<b>'+packs[i].name+'</b>'+packs[i].products_names;

                        // price_nett
                        const td_price_nett = document.createElement("td");
                        td_price_nett.innerHTML = packs[i].price_nett;

                        // price_gross
                        const td_price_gross = document.createElement("td");
                        td_price_gross.innerHTML = packs[i].price_gross;

                        // discount_nett
                        const td_discount_nett = document.createElement("td");
                        td_discount_nett.innerHTML = packs[i].discount_nett;

                        // discount_gross
                        const td_discount_gross = document.createElement("td");
                        td_discount_gross.innerHTML = packs[i].discount_gross;

                        // quantity
                        const td_quantity = document.createElement("td");
                        td_quantity.innerHTML = packs[i].quantity;
                        
                        // active
                        const td_active = document.createElement("td");
                        
                        const input_active = document.createElement("input");
                        input_active.value = packs[i].id;
                        input_active.type = "checkbox";
                        if(packs[i].active == 1) {
                            input_active.checked = 1;
                        }
                        input_active.onchange = function() {
                            changeActive(input_active);
                        }
                        td_active.appendChild(input_active);

                        // set action
                        const td_action = document.createElement("td");
                        
                        const edit = document.createElement("button");
                        edit.className = "btn btn-action editPack";
                        edit.type = "button";
                        edit.textContent = "Edytuj";
                        edit.setAttribute('data-id', packs[i].id);
                        edit.onclick = function() {
                            editPack(edit);
                        }
                        td_action.appendChild(edit);

                        const deletePackButton = document.createElement("button");
                        deletePackButton.className = "btn btn-action deletePack";
                        deletePackButton.type = "button";
                        deletePackButton.textContent = "Usuń";
                        deletePackButton.setAttribute('data-id', packs[i].id);
                        deletePackButton.onclick = function() {
                            deletePack(deletePackButton);
                        }
                        td_action.appendChild(deletePackButton);

                        const td_position = document.createElement("td");

                        const position_up = document.createElement("button");
                        position_up.type = "button";
                        position_up.setAttribute('data-id', packs[i].id);
                        position_up.setAttribute('what-to-do', 'upper');
                        position_up.setAttribute('data-position', packs[i].position);
                        position_up.textContent = '↑';
                        position_up.className = "btn btn-action";
                        position_up.onclick = function() {
                            changePosition(position_up);
                        }
                        td_position.appendChild(position_up);

                        const position_down = document.createElement("button");
                        position_down.type = "button";
                        position_down.setAttribute('data-id', packs[i].id);
                        position_down.setAttribute('what-to-do', 'lower');
                        position_down.setAttribute('data-position', packs[i].position);
                        position_down.textContent = '↓';
                        position_down.className = "btn btn-action";
                        position_down.onclick = function() {
                            changePosition(position_down);
                        }
                        td_position.appendChild(position_down);

                        // add elements into tr
                        tr.appendChild(td_id);
                        tr.appendChild(td_name);
                        tr.appendChild(td_price_nett);
                        tr.appendChild(td_price_gross);
                        tr.appendChild(td_discount_nett);
                        tr.appendChild(td_discount_gross);
                        tr.appendChild(td_quantity);
                        tr.appendChild(td_active);
                        tr.appendChild(td_action);
                        tr.appendChild(td_position);

                        // add tr into table
                        table.appendChild(tr);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Wystąpił błąd:', status, error);
            }
        });
    }

    function createPack() {
        const create = document.querySelector("#createPack");
        
        create.addEventListener('click', function() {
            if(document.querySelector("#pack_name").value == '') {
                alert("Nie wpisano nazwy pakietu.");
                return;
            }
            else if(getProducts().length == 0) {
                alert("Nie wybrano zadnego produktu.");
                return;
            }

            const fileInput = document.querySelector("#packImage");
            const file = fileInput.files[0];

            let id_pack = null;
            if(create.getAttribute('data-id')) {
                id_pack = create.getAttribute('data-id');
            }

            const formData = new FormData();
            formData.append('id_pack', id_pack);
            formData.append('image', file);
            formData.append('name', document.querySelector("#pack_name").value);
            formData.append('price', document.querySelector("#pack_price").value);
            formData.append('id_main_product', '{/literal}{$id_product}{literal}');
            formData.append('for_all_products', String(document.querySelector("#for_all_products").checked));
            formData.append('for_all_cobinations', String(document.querySelector("#for_all_combination").checked));
            formData.append('reduction', parseFloat(document.querySelector("#pack_products_reduction").value));
            formData.append('reduction_type', document.querySelector("#pack_products_reduction_type").value);
            formData.append('products', JSON.stringify(getProducts()));
            formData.append('active', document.querySelector("#activePack").value);

            $.ajax({
                url: '{/literal}{$create_pack}{literal}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    updateList();
                    if(id_pack != null) {
                        alert("Poprawnie zaaktualizowano pakiet.");
                    }
                    else {
                        alert("Utworzono poprawnie pakiet.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Wystąpił błąd:', status, error);
                }
            });
        });
    }

    function getFileExtensionFromUrl(url) {
        return url.split('.').pop().split(/\#|\?/)[0];
    }

    function setFileFromUrl(url) {
        fetch(url)
            .then(response => response.blob())
            .then(blob => {
                // Pobierz rozszerzenie pliku z URL
                const extension = getFileExtensionFromUrl(url);
                const fileName = `pack_photo.${extension}`;

                // Stwórz plik z odpowiednim rozszerzeniem
                const file = new File([blob], fileName, { type: blob.type });

                // Utwórz DataTransfer, aby dodać plik do inputa
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                const fileInput = document.querySelector("#packImage");
                fileInput.files = dataTransfer.files;

                const objectUrl = URL.createObjectURL(file);
            })
            .catch(error => console.error('Błąd podczas pobierania obrazu:', error));
    }

    function setPackData(pack) {
        // set pack name
        document.querySelector("#pack_name").value = pack.name;
        
        // for all products
        document.querySelector("#for_all_products").checked = pack.for_all_products;
        // for all combination
        document.querySelector("#for_all_combination").checked = pack.for_all_combination;
    
        // set price
        document.querySelector("#pack_price").value = pack.price;
        // set reduction
        document.querySelector("#pack_products_reduction").value = pack.reduction;

        // set active 
        document.querySelector("#activePack").value = pack.active;
        
        const selectElement = document.querySelector("#pack_products_reduction_type");
        if(selectElement) {
            for(let i=0; i<selectElement.options.length; i++) {
                if(selectElement.options[i].value === pack.reduction_type) {
                    selectElement.selectedIndex = i;
                    document.querySelector("#reduction_type_sign").innerHTML = selectElement.options[i].getAttribute('data-sign');
                    break;
                }
            }
        }

        // set image
        setFileFromUrl(pack.image);

        // set products
        for(let i=0; i<pack.products.length; i++) {
            createRecordInTable(pack.products[i].id_product, pack.products[i]);
        }

        document.querySelector("#createPack").setAttribute('data-id', pack.id);

        document.querySelector("#without_reduction").style = "display: block; color: red;";
        document.querySelector("#full_price").style = "display: block; color: red;";

        changePrice(document.querySelector("#pack_products_reduction").value, document.querySelector("#pack_products_reduction_type").value);
    }

    function clearFileds() {
        // set pack name
        document.querySelector("#pack_name").value = "";
        document.querySelector("#product_search").value = "";
        
        // for all products
        document.querySelector("#for_all_products").checked = false;
        // for all combination
        document.querySelector("#for_all_combination").checked = false;
    
        // set price
        document.querySelector("#pack_price").value = 0;
        // set reduction
        document.querySelector("#pack_products_reduction").value = 0;
        
        document.querySelector("#packImage").value = '';
        document.querySelector("#choosedProducts").innerHTML = "";
    }

    function editPack(button) {
        clearFileds();
        let id_pack = button.getAttribute('data-id');
        $.ajax({
            url: '{/literal}{$getPackData}{literal}&id_pack='+id_pack+'&id_product_main='+{/literal}{$id_product}{literal},
            type: 'GET',
            success: function(response) {
                setPackData(response.pack);
            },
            error: function(xhr, status, error) {
                console.error('Wystąpił błąd:', status, error);
            }
        });
    }

    function deletePack(button) {
        let id_pack = button.getAttribute('data-id');
        $.ajax({
            url: '{/literal}{$deletePack}{literal}&id_pack='+id_pack,
            type: 'GET',
            success: function(response) {
                if(response) {
                    updateList();
                    alert("Poprawnie usunięto pakiet.");
                }
            },
            error: function(xhr, status, error) {
                console.error('Wystąpił błąd:', status, error);
            }
        });   
    }

    function changeActive(button) {
        let id_pack = button.value;
        let active = 1;
        if(!button.checked) {
            active = 0;
        }

        $.ajax({
            url: '{/literal}{$changeActive}{literal}&id_pack='+id_pack+'&active='+active,
            type: 'GET',
            success: function(response) {
                if(response) {
                    updateList();
                    alert("Poprawnie zmieniono aktywność.");
                }
            },
            error: function(xhr, status, error) {
                console.error('Wystąpił błąd:', status, error);
            }
        });
    }

    function changePosition(button) {
        let id_pack = button.getAttribute('data-id');
        let what_to_do = button.getAttribute('what-to-do');
        let current_position = button.getAttribute('data-position');
        
        $.ajax({
            url: '{/literal}{$changePosition}{literal}&id_pack='+id_pack+'&what_to_do='+what_to_do+'&current_position='+current_position+'&id_product='+{/literal}{$id_product}{literal},
            type: 'GET',
            success: function(response) {
                if(response) {
                    updateList();
                    alert("Poprawnie zmieniono pozycję.");
                }
            },
            error: function(xhr, status, error) {
                console.error('Wystąpił błąd:', status, error);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        searchProduct();
        changeTypeRedution();
        createPack();
        updateEndPriceByInput();
        updateEndPriceByType();
    });

</script>
{/literal}

<style>

    #founded_product_list {
        border: 1px solid #bbcdd2;
        border-radius: 4px;
    }

    #founded_product_list > ul {
        padding: 0px !important;
        margin-top: 2px;
        max-height: 200px;
        overflow: auto;
        margin-bottom: 0px;
    }

    #founded_product_list > ul > li {
        padding: 10px;
        list-style-type: none;
        cursor: pointer;
    }

    #founded_product_list > ul > li:not(:last-child) {
        border-bottom: 1px solid #bbcdd2;
    }

    #founded_product_list > ul > li:hover {
        background-color: #f4fcfd;
    }

</style>