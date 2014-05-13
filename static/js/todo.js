var toDoList = {};

var request;

toDoList.request = {};

toDoList.renderingEnabled = true;

toDoList.indexedDB = {};

toDoList.indexedDB.open = function() {

    var version = 7;
    var request = indexedDB.open("todos", version);

    // We can only create Object stores in a versionchange transaction.
    request.onupgradeneeded = function(e) {
        var db = e.target.result;

        // A versionchange transaction is started automatically.
        e.target.transaction.onerror = toDoList.indexedDB.onerror;

        if (db.objectStoreNames.contains("todo")) {
            db.deleteObjectStore("todo");
        }

        var store = db.createObjectStore("todo",
                {keyPath: "timeStamp"});
    };

    request.onsuccess = function(e) {
        toDoList.indexedDB.db = e.target.result;
        $('#synchronize').click();
    };

    request.onerror = toDoList.indexedDB.onerror;
};

toDoList.indexedDB.addTodo = function(obj) {

    var db = toDoList.indexedDB.db;

    var trans = db.transaction(["todo"], "readwrite");

    var store = trans.objectStore("todo");

    var request = store.put(obj);

    request.onsuccess = function(e) {
        
        toDoList.indexedDB.getAllTodoItems();
    };

    request.onerror = function(e) {
        
        console.log(e.value);
    };
};

toDoList.indexedDB.updateTodo = function(id, obj) {

    id = parseInt(id, 10);

    var db = toDoList.indexedDB.db;

    var trans = db.transaction(["todo"], "readwrite");

    var store = trans.objectStore("todo");

    var cursorRequest = store.openCursor(IDBKeyRange.only(id));

    cursorRequest.onsuccess = function(e) {

        var cursor = cursorRequest.result || e.result;

        console.log(cursor);

        var _object = cursor.value;
        
        for(var index in obj){
            _object[index] = obj[index];
        }

        var request = cursor.update(_object);

        request.onerror = toDoList.indexedDB.onerror;

        request.onsuccess = function(e) {

        };
    }
};

toDoList.indexedDB.getAllTodoItems = function(callback, renderingEnabled) {

    toDoList.todos = [];

    $("#todoItems").empty();

    var db = toDoList.indexedDB.db;

    if (renderingEnabled !== undefined) {
        toDoList.renderingEnabled = renderingEnabled;
    }

    var trans = db.transaction(["todo"], "readwrite");

    var store = trans.objectStore("todo");

    trans.oncomplete = function(evt) {

        toDoList.render = true;

        if (callback !== undefined) {
            callback();
        }
    };

    // Get everything in the store;
    var keyRange = IDBKeyRange.lowerBound(0);

    var cursorRequest = store.openCursor(keyRange);

    cursorRequest.onsuccess = function(e) {

        var result = e.target.result;

        if (!!result == false)
            return;

        toDoList.todos.push(result.value);

        if (toDoList.renderingEnabled === true) {
            renderTodo(result.value);
        }

        result.continue();
    };

    cursorRequest.onerror = toDoList.indexedDB.onerror;
};

toDoList.indexedDB.sync = function() {

    var items = toDoList.todos;

    var el = $("#synchronize");

    var url = el.attr('data-ajaxurl');

    // fire off the request to /form.php
    var request = $.ajax({
        url: url,
        type: "POST",
        data: {todos: JSON.stringify(items)}
    });

    request.done(function(response, textStatus, jqXHR) {

        toDoList.indexedDB.processSyncData(JSON.parse(response));
    });

    request.fail(function(jqXHR, textStatus, errorThrown) {

        toDoList.renderingEnabled = true;

        if (errorThrown === 'Not Found') {
            alert('Invalid URL to Synchronize service.');
        } else {
            alert('Unknown error');
        }
    });

    request.always(function() {

        el.removeAttr('data-ajaxdisabled');
    });
};

toDoList.indexedDB.processSyncData = function(data) {

    for (var index in data.add) {

        toDoList.indexedDB.addTodo(data.add[index]);
    }

    for (var index in data.update) {

        var timeStamp = data.update[index].timeStamp;

        toDoList.indexedDB.updateTodo(timeStamp, data.update[index]);
    }

    setTimeout(function() {
        toDoList.indexedDB.getAllTodoItems(function() {
        }, true);
    }, 300);
};

toDoList.indexedDB.synchronize = function(event) {

    if ($(this).attr('data-ajaxdisabled') === 'yes')
        return;

    $(this).attr('data-ajaxdisabled', 'yes');

    // abort any pending request
    if (request) {
        request.abort();
    }

    toDoList.renderingEnabled = false;

    toDoList.indexedDB.getAllTodoItems(toDoList.indexedDB.sync);

    event.preventDefault();

};

function renderTodo(row) {

    if (row.active === 0) {
        return;
    }

    var $todoValue = $("<div>");

    $todoValue.attr("id", row.timeStamp);

    $todoValue.text(row.text);

    $todoValue.editable(function(value, settings) {
        
        var obj = {
            text: value,
            last_update: currTime()
        };

        toDoList.indexedDB.updateTodo($(this).attr('id'), obj);

        return(value);
    }, {
        type: 'text'
    });

    var $delete = $("<a>").text("Done").attr('href', '#').addClass('btn').addClass('btn-success').addClass('btn-xs').addClass('btn-block');

    $delete.click(function(e) {
        
        var toUpdate = {
            active: 0,
            last_update: currTime()
        };
        
        toDoList.indexedDB.updateTodo(row.timeStamp, toUpdate);
        
        $(this).parent().parent().remove();

        return false;
    });

    var $tr = $("<tr>");

    $tr.append($('<td>').append($todoValue));

    $tr.append($('<td>').append($delete));

    $("#todoItems").append($tr);
}

function addTodo() {
    var todo = document.getElementById('todo');
    
    var time = currTime();
    
    var obj = {
        text: todo.value,
        active: 1,
        timeStamp: time,
        last_update: time
    }

    toDoList.indexedDB.addTodo(obj);
    
    todo.value = '';
    
    return false;
}

$(document).ready(function() {
    
    $('#synchronize').click(toDoList.indexedDB.synchronize);
    
    $('#addtodo').submit(addTodo);

    toDoList.indexedDB.open();

});

function currTime(){
    
    return Math.round(new Date().getTime() / 1000);
}