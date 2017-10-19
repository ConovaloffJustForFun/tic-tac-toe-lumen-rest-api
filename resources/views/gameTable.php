<?php require 'head.php' ?>

<div class="container center">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <form id="searchGameById">
                <label for="searchRepository">Restore game by id:</label>
                <div class="input-group custom-search-form">
                    <input name="query" type="text" id="searchRepository" class="form-control"/>
                    <span class="input-group-btn">
                        <button class="btn btn-default" type="button">
                            <span class="glyphicon glyphicon-search"></span>
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2 game">
            <table class="game">
                <tr>
                    <td data-x="0" data-y="0" class="square"></td>
                    <td data-x="0" data-y="1" class="square v"></td>
                    <td data-x="0" data-y="2" class="square"></td>
                </tr>
                <tr>
                    <td data-x="1" data-y="0" class="square h"></td>
                    <td data-x="1" data-y="1" class="square v h"></td>
                    <td data-x="1" data-y="2" class="square h"></td>
                </tr>
                <tr>
                    <td data-x="2" data-y="0" class="square"></td>
                    <td data-x="2" data-y="1" class="square v"></td>
                    <td data-x="2" data-y="2" class="square"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="information_block">
                <div>Information</div>
                <div class="information_container">
                    <div>id of your table: <span class="id"></span></div>
                    <div>state: <span class="state"></span></div>
                    <div>seconds to close table: <span class="time_to_timeout"></span></div>
                    <div>api answer: <span class="api_answer"></span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/gameTable.js"></script>
<?php require 'footer.php' ?>
