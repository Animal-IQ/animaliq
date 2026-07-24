@php
    $p = $question->payload ?? [];
    $items = $p['items'] ?? $p['correct_order'] ?? [];
    $shuffled = collect($items)->shuffle()->values();
@endphp
@if(!empty($p['criterion']))
    <p class="font-medium theme-text-primary mb-3">{{ $p['criterion'] }}</p>
@endif
<p class="text-sm theme-text-secondary mb-2">Enter the order from top to bottom (one animal per line).</p>
<textarea name="order" rows="{{ max(4, count($shuffled)) }}" class="theme-input w-full" required placeholder="{{ implode("\n", $shuffled->all()) }}"></textarea>
<p class="text-xs theme-text-secondary mt-1">Items to rank: {{ $shuffled->implode(', ') }}</p>
<script>
(function(){
  var ta = document.querySelector('#q-{{ $question->id }} textarea[name=order]');
  if (!ta) return;
  ta.addEventListener('blur', function(){
    // convert textarea lines to hidden inputs named order[] for backend
  });
  ta.form.addEventListener('submit', function(e){
    var lines = ta.value.split(/\r?\n/).map(function(s){return s.trim();}).filter(Boolean);
    ta.removeAttribute('name');
    lines.forEach(function(line){
      var inp = document.createElement('input');
      inp.type = 'hidden';
      inp.name = 'order[]';
      inp.value = line;
      ta.form.appendChild(inp);
    });
  });
})();
</script>
