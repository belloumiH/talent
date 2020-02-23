// $(".select2-selection__rendered").on("DOMSubtreeModified", function () {
//     $('.select2-selection__rendered li').each(function()
//     {
//         alert($(this).attr('title')); // This is your rel value
//     });
// });

$(".update-post-class").on("click", function (event) {
    var idPost = $(this).attr('data-input-id');
    var idLabelPost = $(this).attr('data-input-label');
    var labelPostValue = $('#' + idLabelPost).val();

    $.ajax({
        url: Routing.generate('update.post', {
            idPost: idPost
        }),
        type: 'POST',
        async: true,
        data: {
            "value": labelPostValue,
        },
        success: function (data) {
            window.location.href = Routing.generate('show.post');
        },
        error: function () {
            console.log('error update post');
        }
    });
});

$(".update-skill-class").on("click", function (event) {
    var idSkill = $(this).attr('data-input-id');
    var idLabelSkill = $(this).attr('data-input-label');
    var labelSkillValue = $('#' + idLabelSkill).val();

    $.ajax({
        url: Routing.generate('update.skill', {
            idSkill: idSkill
        }),
        type: 'POST',
        async: true,
        data: {
            "value": labelSkillValue,
        },
        success: function (data) {
            window.location.href = Routing.generate('show.skill');
        },
        error: function () {
            console.log('error update post');
        }
    });
});