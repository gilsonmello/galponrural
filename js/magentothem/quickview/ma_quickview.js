var paths = [];

function hideQVButton(element, ce) {
    $jq(element + ' .btn-quickview-' + ce).hide();
}

function showQVButton(element, ce) {
    $jq(element + ' .btn-quickview-' + ce).show();
}

function closeQVFrame() {
    $jq('#quickview-bg-block').hide();
    $jq('.quickview-load-img').hide();
    $jq('div#quickview-content').hide(600);
    $jq('div#quickview-content').html('');
}

function appendQuickViewEvent(element, ce) {
    var clickEvent = "ajaxView('" + paths[ce] + "')";
    $jq(element + ' .btn-quickview-' + ce).attr('onclick', clickEvent);
}

function appendCloseFrameLink() {
    $jq('div#quickview-content').prepend("<a href='javascript:void(0);' class='a-qv-close' onclick='closeQVFrame()'>Close</a>");
}

function appendQuickViewinListScript() {
    initQuickButton('.category-products');
    $jq(document).ajaxComplete(function(){
        initQuickButton('.category-products');
    });
}

function initQuickButton(element) {
    var count = 0;
    var subpath = "";
    $jq(element + ' a.product-image').each(function(path) {
        var rel = $jq(this).attr('rel');
        if(rel == null) {
            $jq(this).attr('rel', 'author');
            count++;
            path = $jq(this).attr('href');
            subpath = path.substring(path.lastIndexOf('/') + 1, path.length);
            paths[count] = subpath;
            $jq(this).closest("div").append('<div class="qv-button-container"><button type="button" ' +
                                                'class="qv-e-button btn-quickview-' + count + '">' +
                                                Translator.translate('Quick View') +
                                                '</button>' +
                                                 '<em>' + Translator.translate('Quick View') + '</em>' +
                                                 '</div>');
                                        
            appendQuickViewEvent(element, count);
        }
    });
}

function ajaxView(path) {
    $jq('#quickview-bg-block').show();
    $jq('.quickview-load-img').show();
    $jq.ajax({
        url     : MA.QuickView.BASE_URL + 'quickview/index/view/path/' + path,
        type    : "get",
        success : function(response) {
            $jq('div#quickview-content').html(response);
            appendCloseFrameLink();
            $jq('.quickview-load-img').hide();
            $jq('div#quickview-content').show(600);
        }
    });
}

$jq(document).ready(function() {
    if(MA.QuickView.CATEGORY_ENABLE == 'true') {
        $jq('.category-products').append('<script type="text/javascript">appendQuickViewinListScript()</script>');
    }
});




