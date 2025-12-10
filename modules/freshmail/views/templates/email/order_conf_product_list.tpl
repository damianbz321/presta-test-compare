<table
    border="0"
    cellpadding="0"
    cellspacing="0"
    role="presentation"
    width="100%"
    style="
      border-collapse: collapse;
      font-family: 'Open sans', Arial, sans-serif;
      font-size: 14px;
      vertical-align: top;
    "
  >
    <tbody>
      <tr>
        <td
          align="left"
          class="products-table"
          style="
            border-collapse: collapse;
            font-family: 'Open sans', Arial, sans-serif;
            color: rgb(53, 57, 67);
            font-size: 0px;
            padding: 10px 30px;
            word-break: break-word;
          "
        >
          <table
            style="
              border-collapse: collapse;
              color: #000000;
              background-color: #ffffff;
              font-family: 'Open sans', arial, sans-serif;
              font-size: 14px;
              line-height: 22px;
              table-layout: auto;
              width: 100%;
            "
            border="0"
            width="100%"
            cellspacing="0"
            cellpadding="0"
          >
            <colgroup>
              <col style="width: 15%" span="1" width="15%" />
              <col style="width: 55%" span="1" width="55%" />
              <col style="width: 15%" span="1" width="15%" />
              <col style="width: 15%" span="1" width="15%" />
            </colgroup>
            <tbody>
              <tr>
                <th
                  style="
                    font-family: 'Open sans', Arial, sans-serif;
                    font-size: 12px;
                    background-color: #fdfdfd;
                    color: #353943;
                    font-weight: 600;
                    padding: 10px 10px;
                    border: 1px solid #dfdfdf;
                    text-align: left;
                  "
                  colspan="2"
                  bgcolor="#FDFDFD"
                >
                  Produkt
                </th>
                <th
                  style="
                    font-family: 'Open sans', Arial, sans-serif;
                    font-size: 12px;
                    background-color: #fdfdfd;
                    color: #353943;
                    font-weight: 600;
                    padding: 10px 10px;
                    border: 1px solid #dfdfdf;
                    text-align: center;
                  "
                  bgcolor="#FDFDFD"
                >
                  Ilość
                </th>
                <th
                  style="
                    font-family: 'Open sans', Arial, sans-serif;
                    font-size: 12px;
                    background-color: #fdfdfd;
                    color: #353943;
                    font-weight: 600;
                    padding: 10px 10px;
                    border: 1px solid #dfdfdf;
                    text-align: center;
                  "
                  bgcolor="#FDFDFD"
                >
                  Cena
                </th>
              </tr>
				{foreach $list as $product}
				<tr>
					<td style="border: 1px solid #d6d4d4; padding: 10px">
								<img
									src="{$product['img']}"
									alt="{$product['name']}"
									width="50"
								/>
								</td>
								<td style="border: 1px solid #d6d4d4; padding: 10px 20px">
								<strong>{$product['name']}</strong>
								</td>
								<td
								style="
									border: 1px solid #d6d4d4;
									text-align: center;
									padding: 10px;
								"
								>
								{$product['quantity']}
								</td>
								<td
								style="
									border: 1px solid #d6d4d4;
									text-align: center;
									padding: 10px;
								"
								>
								{Tools::ps_round($product['price_wt'],2)}
								</td>
				</tr>
				{/foreach}
            </tbody>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
