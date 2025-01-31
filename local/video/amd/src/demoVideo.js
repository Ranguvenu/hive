/**
 * local courses
 *
 * @package    local_courses
 * @copyright  2019 eAbyas <eAbyas.in>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 define(['local_courses/jquery.dataTables','jquery',
 'core/str',
 'core/modal_factory',
 'core/modal_events',
 'core/fragment',
 'core/ajax',
 'jqueryui'], function (DataTables,$, Str, ModalFactory, ModalEvents, Fragment, Ajax) {
    var demoVideo = function (args) {
        var self = this;
        this.args = args;
    };


    demoVideo.prototype.modal = null;
    return /** @alias module:local_video/demoVideo */ {
        // Public variables and functions.
        /**
        * @param {string} args
        * @return {Promise}
        */
        init: function (args) {
            return new demoVideo(args);
        },
        loadVideo:function (args) {
            this.args = args;
            $('.modal').remove();
            var trigger = $('#demoVideo');
            ModalFactory.create({
                title: this.args.title,
                body: '<video width="100%" height="100%" controls><source src="'+this.args.url+'" type="video/mp4"></video>',
                footer: 'Demo Video',
                large: true,
            }, trigger)
            .done(function(modal) {
                // Do what you want with your new modal.
                /*  var root = modal.getRoot();
                root.on(ModalEvents.hide, function() {
                 alert();
                // Do something to delete item
                });

                $(".close").click(function(){
                    alert();
                    var video = document.getElementById("videoplayer");
                    video.pause();
                }); */
            });          
        },
        load: function () {
            Str.get_strings([{
                key: 'search',
            }]).then(function (str) {
                $('#example').dataTable({
                    "autoWidth": false,
                    "searching": true,
                    "lengthMenu": [[10, 15, 25, 50, 100, -1], [10, 15, 25, 50, 100, "All"]],
                    "aoColumnDefs": [{ 'bSortable': false, 'aTargets': [1] }],
   
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: str[0],
                        "paginate": {
                            "next": ">",
                            "previous": "<"
                        }
                    }
                });
            }.bind(this));  
    
        }, 
        show: function(args) {
          
            args.confirm = true;
            var promise = Ajax.call([{
                methodname: 'local_video_show',
                args: {
                    id: args.id,
                    name: args.name,
                },
            }]);
            promise[0].done(function() {
               window.location.reload();
            });
        }

    };

});
