<section>
  <select name="suboption" id="suboption" onchange="saveIssuer(this.value)">
		{foreach from=$issuers key=id item=issuer}
                    {if $selected == $id}
			<option value="{$id}" selected="selected">{$issuer}</option>
                     {else}
                         <option value="{$id}">{$issuer}</option>
                    {/if}
		{/foreach}	
  </select>
  <br><br>
  <script type="text/javascript">
      function saveIssuer(value){
        var cookieName = 'issuer';
        var cookieValue = value;
        var myDate = new Date();
        myDate.setMonth(myDate.getMonth() + 12);
        document.cookie = cookieName +"=" + cookieValue + ";expires=" + myDate;
      }
  </script>
</section>
  
