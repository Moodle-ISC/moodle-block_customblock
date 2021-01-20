// Standard license block omitted.
/*
 * @package    block_overview
 * @copyright  2015 Someone cool
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_customblock/helloworld
 */

define(['jquery'], function($) {

    return {
        init: function() {
            $('p-3').mouseover(function() {
                $('p-3').css('color', 'red');
            });
        }
    };
});