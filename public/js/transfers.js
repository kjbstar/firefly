$('input[name="beneficiary"]').typeahead({
    name: 'beneficiary',
    prefetch: 'home/beneficiary/typeahead',
    limit: 10
});
$('input[name="category"]').typeahead({
    name: 'category',
    prefetch: 'home/category/typeahead',
    limit: 10
});

$('input[name="budget"]').typeahead({
    name: 'budget',
    prefetch: 'home/budget/typeahead',
    limit: 10
});
