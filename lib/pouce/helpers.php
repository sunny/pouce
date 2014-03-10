<?php

// Helper Alias to escape HTML entities in the view
// Example: h("42>0") # => "42&gt;0"
function h($t) {
  return htmlspecialchars($t);
}

