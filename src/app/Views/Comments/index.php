<?php
use CodeIgniter\Pager\Pager;
/** @var array $comments */
/** @var Pager $pager */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Комментарии</title>
    <meta name="description" content="The small framework with powerful features">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <style>
        .actions .delete {
            cursor: pointer;
        }
        .actions .delete:hover {
            color: #dd4814;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="container" id="comments-list">
        <div class="row d-flex justify-content-center text-center">
            <div class="col-md-8 col-lg-6 my-3">
                <h1>
                    Comments
                </h1>
                <div class="row">
                    <div class="col-md-8 col-lg-6">
                        <label>
                            Sort by
                            <select class="custom-select sort-select">
                                <option
                                    value="id"
                                    <?= empty($_GET["sort"]) || $_GET["sort"] === 'id'
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    ID
                                </option>
                                <option
                                    value="date"
                                    <?= $_GET["sort"] === 'date'
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Date
                                </option>
                            </select>
                        </label>
                    </div>
                    <div class="col-md-8 col-lg-6">
                        <label>
                            Order by
                            <select class="custom-select order-select">
                                <option
                                    value="asc"
                                    <?= empty($_GET["order"]) || $_GET["order"] === 'asc'
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Asc
                                </option>
                                <option
                                    value="desc"
                                    <?= $_GET["order"] === 'desc'
                                        ? 'selected'
                                        : ''
                                    ?>
                                >
                                    Desc
                                </option>
                            </select>
                        </label>
                    </div>
                </div>
                <?php if (!empty($comments)): ?>
                    <div class="card shadow-0 my-3 border" style="background-color: #f0f2f5;">
                        <div class="card-body p-4">
                            <?php foreach ($comments as $comment): ?>
                                <div class="card mb-4" id="comment-<?=$comment['id']?>">
                                    <div class="card-body">
                                        <p><?=$comment["text"]?></p>
                                        <div class="d-flex flex-row justify-content-between">
                                            <p class="small mb-0 ms-2"><?=$comment["name"]?></p>
                                            <p class="small mb-0 ms-2"><?=$comment["date"]?></p>
                                        </div>
                                        <div class="actions d-flex flex-row justify-content-end mt-2">
                                        <span class="delete" data-comment-id="<?=$comment["id"]?>">
                                            delete
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                <p>There is no comments. Be first!</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <?= $pager->links('default', 'custom') ?>
        </div>
    </div>
</div>
<div class="container form-container">
    <h2>Add new comment</h2>
    <div class="alert alert-danger d-none" role="alert"></div>
    <form id="comment-add" action="/" method="post">
        <?= csrf_field() ?>
        <div class="form-group">
            <label for="name">Email</label>
            <input type="email"
                   class="form-control"
                   id="name"
                   name="name"
                   value="<?php set_value('email') ?>"
                   aria-describedby="emailHelp"
                   required
                   placeholder="Enter email">
        </div>
        <div class="form-group">
            <label for="comment-message">Comment</label>
            <textarea class="form-control"
                      id="comment-message"
                      placeholder="Your comment"
                      name="text"
                      required
            ><?php set_value('text') ?></textarea>
        </div>
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date"
                   class="form-control"
                   id="date"
                   name="date"
                   required
                   value="<?php set_value('date') ?>"
            >
        </div>
        <button type="submit" class="btn btn-primary btn-submit">Submit</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-U1DAWAznBHeqEIlVSCgzq+c9gqGAJn5c/t99JyeKa9xxaYpSvHU5awsuZVVFIhvj" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    const updateComments = () => {
        const sort = $('.sort-select').val()
        const order = $('.order-select').val()
        const urlParams = new URLSearchParams(window.location.search)
        if (sort) {
            urlParams.set("sort", sort)
        }
        if (order) {
            urlParams.set("order", order)
        }
        let url = "/"
        if (urlParams.toString()) {
            url += "?" + urlParams.toString()
        }
        $.ajax({
            url: url,
            method: "get",
            dataType: "html",
            success: (response) => {
                const tempDiv = $('<div>').html(response)
                const newCommentsList = tempDiv.find('#comments-list')
                $('#comments-list').html(newCommentsList.html())
                deleteCommentEventSet()
                sortCommentsEventSet()
            },
            error: (jqXHR, textStatus, error) => {
                console.error(jqXHR.responseText, textStatus, error)
            }
        })
    }

    const handleFormError = (show, error) => {
        if (show) {
            $(".form-container .alert-danger").removeClass("d-none")
            $(".form-container .alert-danger").html(error)
        } else {
            $(".form-container .alert-danger").addClass("d-none")
            $(".form-container .alert-danger").html("")
        }
    }

    const deleteCommentEventSet = () => {
        // on delete comment event handler
        $('.card .actions .delete').on("click", function() {
            const id = $(this).data("commentId");
            if (!id) alert("Incorrect comment id")
            if (confirm("Do you want to delete this comment?")) {
                $.ajax({
                    url: "/"+id,
                    method: "delete",
                    dataType: "json",
                    success: (response) => {
                        if (response.status) {
                            $('#comment-'+id).remove()
                            updateComments()
                        } else {
                            alert(response.error)
                        }
                    },
                    error: (jqXHR, textStatus, error) => {
                        console.error(jqXHR.responseText, textStatus, error)
                    }
                })
            }
        })
    }

    const sortCommentsEventSet = () => {
        // add sort event handler
        $('.sort-select').on("change", function() {
            updateComments()
        })
        // add order event handler
        $('.order-select').on("change", function() {
            updateComments()
        })
    }

    $(document).ready(function() {
        deleteCommentEventSet()
        sortCommentsEventSet()

        // form on add comment event handler
        $('#comment-add').on("submit", function(e) {
            e.preventDefault()
            $.ajax({
                url: "/",
                method: "post",
                dataType: "json",
                data: $(this).serialize(),
                success: (response) => {
                    if (response.status) {
                        $("#comment-add")[0].reset()
                        updateComments()
                        handleFormError(false, "")
                    } else {
                        let error = "";
                        for (const property in response.errors) {
                            if (response.errors[property]) {
                                error += response.errors[property] + '<br>'
                            }
                        }
                        handleFormError(true, error)
                    }
                },
                error: (jqXHR, textStatus, error) => {
                    console.error(jqXHR.responseText, textStatus, error)
                }
            })
        })
    })
</script>
</body>
</html>
