
$(".pagination li").click(function() {
    if($(this).hasClass('active')) return;
    page($(this).find("a").data("page"));
});

function page(n) {
    if(!n || n < 1 || n > maxPage) return;
    var $form = $("form.pagination");
    $form.find("input[name='page']").first().val(n);
    $form.submit();
}
 