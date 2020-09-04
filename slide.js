(function($) {
 
    var fadeInOut = function ($element) {
        var $children = $element.children(),
            current = 0,
            time = 8000,
            speed = '2s';
      
        $children.css({ transition: speed })
                 .eq(current).css({ opacity: '1' });
      
        setInterval(function () {
            $children.eq(current).css({ opacity: '0' });
            current = current === $children.length - 1 ? 0 : current += 1;
            $children.eq(current).css({ opacity: '1' });
        }, time);
    };
 
    fadeInOut($('#cover'));
 
})(jQuery);
