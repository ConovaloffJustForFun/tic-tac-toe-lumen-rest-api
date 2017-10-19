function GameTable(api_url_prefix, game_element, id) {
    var self = this;

    self.sets = {
        0: '',
        1: 'X',
        2: 'O',
        3: 'DRAW'
    };

    self.api_url_prefix = api_url_prefix;
    self.game_element = game_element;
    self.id = id;

    self.information_container = $('.information_container');
    // $('[data-y="0"][data-x="0"]')

    self.set_on_click = function () {
        self.game_element.click(function (e) {
            var x = $(e.target).data('x');
            var y = $(e.target).data('y');

            var request = {
                'x': x,
                'y': y
            };

            $.post(self.api_url_prefix + '/' + self.id, request, function (response) {
                self.information_container.find('.api_answer').text(JSON.stringify(response));
                if (!response.data) {
                    return false;
                }

                data = response.data;

                if (data.field) {
                    self.setField(data.field);
                }

                if (data.winner) {
                    self.information_container.find('.state').text('winner: ' + self.sets[data.winner]);
                }

                self.information_container.find('.time_to_timeout').text(data.time_to_timeout);
            });
        });

        $('form#searchGameById').submit(function (e) {
            var game_new_id = $(e.target).find('input').val();
            self.create_new_game_by_id(game_new_id);
            return false;
        });
    };

    self.__contruct = function () {

        self.set_on_click();

        if (typeof self.id === "undefined") {
            self.create_new_game();
        }
    };

    self.setField = function (field) {
        $.each(field, function (x, x_line) {
            $.each(x_line, function (y, tile_value) {
                var tile = $('[data-x="' + x + '"][data-y="' + y + '"]');
                tile.text(self.sets[tile_value]);
            });
        });
    };

    self.create_new_game = function () {
        $.post(self.api_url_prefix, function (response) {
            self.information_container.find('.api_answer').text(JSON.stringify(response));
            if (!response.data) {
                return false;
            }

            data = response.data;
            self.setField(data.field);
            self.id = data.id;

            self.information_container.find('.id').text(self.id);
            self.information_container.find('.state').text('open');
        });
    };

    self.create_new_game_by_id = function (id) {
        $.get(self.api_url_prefix + '/' + id, function (response) {
            self.information_container.find('.api_answer').text(JSON.stringify(response));
            if (!response.data) {
                return false;
            }

            var data = response.data;
            self.setField(data.field);
            self.id = id;

            self.information_container.find('.id').text(self.id);
            self.information_container.find('.time_to_timeout').text(data.time_to_timeout);
            self.information_container.find('.state').text(data.is_open ? 'open': 'close');
        });
    };

    self.__contruct()
}

var gameTable = new GameTable('/api/table', $('table.game'));
