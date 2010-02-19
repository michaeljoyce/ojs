{**
* advancedSearch.tpl
*
* Copyright (c) 2003-2009 John Willinsky
* Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
*
* Site/journal advanced search form.
*
* $Id: advancedSearch.tpl,v 1.29 2009/05/26 01:31:18 mcrider Exp $
*}
{strip}
{assign var="pageTitle" value="navigation.annotationSearch"}
{include file="common/header.tpl"}
{/strip}

{if !$isUserLoggedIn}
<p>{translate key="annotations.mustLogin"} [<a href="{url page="login"}" target="_parent">{translate key="navigation.login"}</a>]</p>
{else}
<script type="text/javascript">
    {literal}
    <!--
    function ensureKeyword() {
        var allBlank = document.search.query.value == '';

        if (allBlank) {
            alert({/literal}'{translate|escape:"jsparam" key="search.noKeywordError"}'{literal});
            return false;
        }
        document.search.submit();
        return true;
    }
    // -->
    {/literal}
</script>

{if !$dateFrom}
{assign var="dateFrom" value="--"}
{/if}

{if !$dateTo}
{assign var="dateTo" value="--"}
{/if}
<div id="annotationSearch">
    <form method="post" name="search" action="{url op="results"}">
          <table class="data" width="100%">
            <tr valign="top">
                <td width="25%" class="label"><label for="query">{translate key="search.annotationSearchFor"}</label></td>
                <td width="75%" class="value"><input type="text" id="query" name="query" size="40" maxlength="255" value="{$query|escape}" class="textField" /></td>
            </tr>
            <tr valign="top">
                <td class="label"><label for="author">{translate key="search.annotationTypes"}</label></td>
                <td class="value"><select name="annotationType">
                        <option value="all">{translate key="common.all"}</option>
                        <option value="lemmas">{translate key="rt.annotations.highlight"}</option>
                        <option value="notes">{translate key="rt.annotations.note"}</option>
                    </select></td>
            </tr>

            <tr valign="top">
                <td class="formSubLabel"><h4>{translate key="search.date"}</h4></td>
                <td>&nbsp;</td>
            </tr>
            <tr valign="top">
                <td class="label">{translate key="search.dateFrom"}</td>
                <td class="value">{html_select_date prefix="dateFrom" time=$dateFrom all_extra="class=\"selectMenu\"" year_empty="" month_empty="" day_empty="" start_year="$startYear" end_year="$endYear"}</td>
            </tr>
            <tr valign="top">
                <td class="label">{translate key="search.dateTo"}</td>
                <td class="value">
		{html_select_date prefix="dateTo" time=$dateTo all_extra="class=\"selectMenu\"" year_empty="" month_empty="" day_empty="" start_year="$startYear" end_year="$endYear"}
                    <input type="hidden" name="dateToHour" value="23" />
                    <input type="hidden" name="dateToMinute" value="59" />
                    <input type="hidden" name="dateToSecond" value="59" />
                </td>
            </tr>
        </table>

        <p><input type="button" onclick="ensureKeyword();" value="{translate key="common.search"}" class="button defaultButton" /></p>

        <script type="text/javascript">
            <!--
            document.search.query.focus();
            // -->
        </script>
    </form>
</div>
<div>The following instructions don't really apply.</div>
{translate key="search.syntaxInstructions"}
{/if}
{include file="common/footer.tpl"}
