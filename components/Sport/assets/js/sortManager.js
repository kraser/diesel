(function($)
{
    var SortManager = function ()
    {
        var bindEvents = function ()
        {
            $(document).on('change', '#seasons', handlerEvent.changeSeason)
            $(document).on('click', 'a.inTab', handlerEvent.changePlayer)
        };
        var handlerEvent =
        {
            sort: function ( event )
            {
                event.preventDefault();
                var $this = $(event.target);
                alert($this.data('name'))
            },
            changeSeason: function ( event )
            {
                var $this = $(event.target);
                var regExp = /\/tourney\/([\d]{1,4})/;
                var url = window.location.href;
                var found = url.match(regExp);
                if ( found && found.length > 1 )
                    url = url.replace(regExp, "/tourney/" + $this.val());
                else
                    url = url.replace(/\/*$/, "/tourney/" + $this.val());
                window.location = url;
            },
            changePlayer: function ( event )
            {
                var $this = $(event.target);
                var id = $this.data('nom');
                var alias = $this.data('alias');
                $("li.nominant[data-alias='" + alias + "']").hide();
                $("li.nominant[data-id='" + id + "']").show();
            }
        };

        this.init = function ()
        {
            bindEvents();
        };
    };

    $(document).ready( function()
    {
        new SortManager().init();
    });

})(jQuery);