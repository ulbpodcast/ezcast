<?php

/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 *            Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
 * UI Design by Julien Di Pietrantonio
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

/**
 * Library used to send and retreive threads (and comments) informations between the database and the server
 * @package ezcast.ezplayer.lib.thread
 */
require_once '../commons/config.inc';
require_once '../commons/lib_database.php';

$stmts = threads_statements_get();
db_prepare($stmts);

function threads_statements_get()
{
    return array(
        /*         * ******* T H R E A D S ******** */
        'thread_insert' =>
        'INSERT INTO ' . db_gettable('threads') .
        ' (title, message, timecode, authorId, authorFullName, creationDate, lastEditDate, lastEditAuthor, studentOnly, albumName, assetName, assetTitle) ' .
        'VALUES (:title, :message, :timecode, :authorId, :authorFullName, :creationDate, :lastEditDate, :lastEditAuthor, :studentOnly, :albumName, :assetName, :assetTitle)',
        'thread_update' =>
        'UPDATE ' . db_gettable('threads') . ' SET title = :title, message = :message, timecode = :timecode ' .
        'WHERE id = :id',
        'threads_select_all' =>
        'SELECT * FROM ' . db_gettable('threads')
        . ' WHERE albumName like :albumName and assetName like :assetName '
        . 'AND deleted = "0" ORDER BY timecode',
        'thread_select_by_id' =>
        'SELECT * FROM ' . db_gettable('threads')
        . ' WHERE id = :id AND deleted = "0"',
        'thread_delete_by_id' =>
        'UPDATE ' . db_gettable('threads')
        . ' SET deleted = :deleted '
        . ' WHERE id = :id LIMIT 1',
        'threads_select_by_album' =>
        'SELECT * FROM ' . db_gettable('threads')
        . ' WHERE albumName like :albumName and deleted = "0" '
        . 'ORDER BY lastEditDate DESC',
        'threads_select_by_asset' =>
        'SELECT * FROM ' . db_gettable('threads')
        . ' WHERE albumName like :albumName AND assetName like :assetName AND deleted = "0" '
        . 'ORDER BY timecode',
        'thread_update_lastEdit' =>
        'UPDATE ' . db_gettable('threads')
        . ' SET lastEditDate = :lastEditDate, lastEditAuthor = :lastEditAuthor '
        . 'WHERE id = :id',
        'thread_inc_nbComments' =>
        'UPDATE ' . db_gettable('threads')
        . ' SET nbComments = nbComments + 1 '
        . 'WHERE id = :id',
        'thread_dec_nbComments' =>
        'UPDATE ' . db_gettable('threads')
        . ' SET nbComments = nbComments - 1 '
        . 'WHERE id = :id',
        'thread_reinit_nbComments' =>
        'UPDATE ' . db_gettable('threads')
        . ' SET nbComments = 0 '
        . 'WHERE id = :id',
        /*         * ******* C O M M E N T S ******** */
        'comment_insert' =>
        'INSERT INTO ' . db_gettable('comments') .
        ' (message, thread, authorId, authorFullName, creationDate, lastEditDate) ' .
        ' VALUES (:message, :thread, :authorId, :authorFullName, :creationDate, :lastEditDate)',
        'comment_insert_reply' =>
        'INSERT INTO ' . db_gettable('comments') .
        ' (message, thread, authorId, authorFullName, creationDate, lastEditDate, parent) ' .
        ' VALUES (:message, :thread, :authorId, :authorFullName, :creationDate, :lastEditDate, :parent)',
        'comment_update' =>
        'UPDATE ' . db_gettable('comments')
        . ' SET message = :message, lastEditDate = :lastEditDate '
        . 'WHERE id = :id',
        'comment_update_nbChild_up' =>
        'UPDATE ' . db_gettable('comments')
        . ' SET nbChilds = nbChilds+1 WHERE id = :id',
        'comment_update_nbChild_down' =>
        'UPDATE ' . db_gettable('comments')
        . ' SET nbChilds = nbChilds-1 WHERE id = :id',
        'comment_select_by_threadId' =>
        'SELECT * FROM ' . db_gettable('comments')
        . ' WHERE thread = :thread AND deleted = "0"',
        'comment_delete_by_id' =>
        'UPDATE ' . db_gettable('comments')
        . ' SET deleted = "1" '
        . 'WHERE id = :id',
        'comment_select_by_id' =>
        'SELECT * FROM ' . db_gettable('comments')
        . ' WHERE id = :id LIMIT 1',
        'comment_children_get' =>
        'SELECT * FROM ' . db_gettable('comments')
        . ' WHERE parent = :parent',
        'comment_delete_by_thread' =>
        'UPDATE ' . db_gettable('comments')
        . ' SET deleted = "1" WHERE thread = :thread',
        'comment_update_approval' =>
        'UPDATE ' . db_gettable('comments')
        . ' SET approval = :approval WHERE id = :id',
        'comment_select_best' =>
        'SELECT * FROM ' . db_gettable('comments') .
        ' WHERE thread = :thread '
        . 'AND score > 0 '
        . 'AND deleted = "0" ORDER BY score DESC LIMIT 1',
        'comment_score_init' =>
        'UPDATE ' . db_gettable('comments') .
        ' SET score = 0 , upvoteScore = 0, downvoteScore = 0 '
        . 'WHERE id = :id',
        'comment_update_score_up' =>
            'UPDATE ' . db_gettable('comments') .
            ' SET score = score+1 , upvoteScore = upvoteScore+1 '
            . 'WHERE id = :id',
        'comment_update_score_down' =>
            'UPDATE ' . db_gettable('comments') .
            ' SET score = score-1 , downvoteScore = downvoteScore+1 '
            . 'WHERE id = :id',
        'comment_update_score_double_up' =>
            'UPDATE ' . db_gettable('comments') .
            ' SET score = score+2 , upvoteScore = upvoteScore+2 '
            . 'WHERE id = :id',
        'comment_update_score_double_down' =>
            'UPDATE ' . db_gettable('comments') .
            ' SET score = score-2 , downvoteScore = downvoteScore+2 '
            . 'WHERE id = :id',
        /*         * ******* V O T E S ******** */
        'vote_insert' =>
            'REPLACE INTO ' . db_gettable('votes') .
            ' (login, comment, voteType) ' .
            'VALUES (:login, :comment, :voteType)',
        'vote_user_get' =>
            'SELECT COUNT(*) AS row, voteType FROM ' . db_gettable('votes') . ' ' .
            'WHERE login = :login AND comment = :comment',
        'vote_cancel' =>
            'DELETE FROM ' . db_gettable('votes') . ' ' .
            'WHERE login = :login AND comment = :comment',
        'vote_delete' =>
            'DELETE FROM ' . db_gettable('votes') .
            ' WHERE comment = :comment',
    );
}

// ===== T H R E A D S
/**
 * Inserts  a thread into the database
 * @global type $db
 * @param type $values
 * @return boolean errorflag
 */
function thread_insert($values)
{
    global $statements;
    global $db_object;
    //    foreach ($values as $row => $value) {
    //        $statements['thread_insert']->bindParam(':'.$row, $value);
    //    }
    $statements['thread_insert']->bindParam(':title', $values['title']);
    $statements['thread_insert']->bindParam(':message', $values['message']);
    $statements['thread_insert']->bindParam(':timecode', $values['timecode']);
    $statements['thread_insert']->bindParam(':authorId', $values['authorId']);
    $statements['thread_insert']->bindParam(':authorFullName', $values['authorFullName']);
    $statements['thread_insert']->bindParam(':creationDate', $values['creationDate']);
    $statements['thread_insert']->bindParam(':lastEditDate', $values['lastEditDate']);
    $statements['thread_insert']->bindParam(':lastEditAuthor', $values['authorFullName']);
    $statements['thread_insert']->bindParam(':studentOnly', $values['studentOnly']);
    $statements['thread_insert']->bindParam(':albumName', $values['albumName']);
    $statements['thread_insert']->bindParam(':assetName', $values['assetName']);
    $statements['thread_insert']->bindParam(':assetTitle', $values['assetTitle']);


    $res = $statements['thread_insert']->execute();
    cache_album_threads_unset($values['albumName']);
    log_append('Create thread: ', 'Create thread with title = ' . $values['title']);

    return $res;
}

/**
 * Updates the title, message and timecode of a thread
 * @global null $db
 * @param type $id
 * @param type $title
 * @param type $message
 * @param type $timecode
 * @param type $album
 * @param type $asset
 * @return boolean
 */
function thread_update($id, $title, $message, $timecode, $album, $author = 'Unknown')
{
    global $statements;

    $date = date('Y-m-d H:i:s');
    $statements['thread_update']->bindParam(':title', $title);
    $statements['thread_update']->bindParam(':message', $message);
    $statements['thread_update']->bindParam(':timecode', $timecode);
    $statements['thread_update']->bindParam(':id', $id);

    $res = $statements['thread_update']->execute();
    thread_update_lastEdit($id, $author);
    cache_album_threads_unset($album);
    log_append('Edit thread: ', 'Edit thread with id = ' . $id);
    return $res;
}

/**
 * Returns an array with all threads
 * @global null $db
 * @return Array
 */
function thread_select_all()
{
    global $statements;

    $statements['threads_select_all']->execute();
    return $statements['threads_select_all']->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Returns an array with all threads published on the asset
 * @global type $db
 * @param string $album
 * @param string $asset
 * @return array
 */
function threads_select_by_asset($album, $asset)
{
    global $statements;

    if (cache_asset_threads_isset($album, $asset)) {
        return cache_asset_threads_get($album, $asset);
    }

    $statements['threads_select_by_asset']->bindParam(':assetName', $asset);
    $statements['threads_select_by_asset']->bindParam(':albumName', $album);
    $statements['threads_select_by_asset']->execute();

    $res = $statements['threads_select_by_asset']->fetchAll(PDO::FETCH_ASSOC);

    cache_asset_threads_set($album, $asset, $res);

    return $res;
}

/**
 * Returns the given thread
 * @global type $db
 * @global type $current_thread
 * @return type
 */
function thread_select_by_id($id = '')
{
    global $statements;
    if ($id == '') {
        return false;
    }

    $statements['thread_select_by_id']->bindParam(':id', $id);
    $statements['thread_select_by_id']->execute();
    $res = $statements['thread_select_by_id']->fetch(PDO::FETCH_ASSOC);

    return $res;
}

/**
 * Mark a thread as deleted
 * @global type $db
 * @param type $id
 * @param type $album
 * @param type $asset
 * @return boolean errorflag
 */
function thread_delete_by_id($id, $album, $asset)
{
    global $statements;
    $statements['thread_delete_by_id']->bindValue(':deleted', '1');
    $statements['thread_delete_by_id']->bindParam(':id', $id);
    $res = $statements['thread_delete_by_id']->execute();

    cache_album_threads_unset($album);
    log_append('Delete thread: ', 'Delete thread with id = ' . $id);

    return $res;
}

/**
 * Return x last updated threads as an array
 * @global type $db
 * @param type $album
 * @return type
 */
function threads_select_by_album($album, $limit)
{
    global $statements;

    if (cache_album_threads_isset($album)) {
        return cache_album_threads_get($album, $limit);
    }

    $statements['threads_select_by_album']->bindParam(':albumName', $album);
    $statements['threads_select_by_album']->execute();
    $res = $statements['threads_select_by_album']->fetchAll(PDO::FETCH_ASSOC);

    cache_album_threads_set($album, $res);
    if ($limit == 0) {
        return $res;
    }
    return array_slice($res, 0, $limit);
}

/**
 * Updates a thread's edit datetime
 * @global type $db
 * @param type $_id
 * @return type
 */
function thread_update_lastEdit($id, $author = "Unknown")
{
    global $statements;
    $date = date('Y-m-d H:i:s');
    $statements['thread_update_lastEdit']->bindParam(':lastEditDate', $date);
    $statements['thread_update_lastEdit']->bindParam(':lastEditAuthor', $author);
    $statements['thread_update_lastEdit']->bindParam(':id', $id);
    $res = $statements['thread_update_lastEdit']->execute();

    return $res;
}

/**
 * Updates number of comments for a thread
 * @global type $db
 * @param type $_id
 * @return type
 */
function thread_update_nbComments($id, $up)
{
    global $statements;
    if ($up) {
        $statements['thread_inc_nbComments']->bindParam(':id', $id);
        $res = $statements['thread_inc_nbComments']->execute();
    } else {
        $statements['thread_dec_nbComments']->bindParam(':id', $id);
        $res = $statements['thread_dec_nbComments']->execute();
    }

    return $res;
}

/**
 * Set nbComments to 0
 * @global type $db
 * @param type $_id
 * @return type
 */
function thread_reinit_nbComments($id)
{
    global $statements;
    $statements['thread_reinit_nbComments']->bindParam(':id', $id);
    $res = $statements['thread_reinit_nbComments']->execute();

    return $res;
}

function thread_search($words, $fields, $albums, $asset = '')
{
    global $db_object;

    // set $relevancy to true if the result is aimed to be sorted by relevancy.
    // With $relevancy = false, as soon as a word is found in any of the fields,
    // we stop the search and check for the next discussion.
    // With $relevancy = true, we search for every words in every fields and
    // give a score to each discussion, according to certain rules.
    $relevancy = true;

    if (count($fields) <= 0) {
        return null;
    }
    if (count($albums) <= 0) {
        return null;
    }

    $albums_in_query = implode(',', array_fill(0, count($albums), '?'));


    $where_base = 'WHERE ' .
            'albumName IN (' . $albums_in_query . ') ' .
            ((isset($asset) && $asset != '') ? ' AND assetName LIKE ' . $db_object->quote("%$asset%") . ' ' : ' ') .
            ((acl_has_moderated_album() && !acl_is_admin()) ? ' AND studentOnly = 0 ' : ' ') .
            'AND c.deleted = 0 ';

    if (in_array('title', $fields)) {
        // search in threads titles
        
        $where = $where_base;
        if (count($words) > 0) {
            $where .= ' AND ( ';
            foreach ($words as $index => $word) {
                if ($index > 0) {
                    $where .= ' OR ';
                }
                $where .= 'title LIKE ' . $db_object->quote("%" . $word . "%") . ' ';
            }
            $where .= ') ';
        }

        /*
         * SELECT DISTINCT ... FROM threads
         * WHERE albumName IN ( ' ... ' )  <-- selection of albums
         * AND assetName LIKE '%$asset%' <-- if $asset != ''
         * AND studentOnly == 0 <-- if user is a teacher
         * AND deleted != 1
         * AND (title LIKE '%$word[i]%' OR title LIKE '%word[i+1]%' ...)
         * GROUP BY albumName, assetName, id;
         */

        $stmt = 'SELECT DISTINCT id, title, message, timecode, albumName, assetName, assetTitle, studentOnly '
                . 'FROM ' . db_gettable('threads') . ' c '
                . $where
                . 'GROUP BY albumName, assetName, id';

        $prepared_stmt = $db_object->prepare($stmt);
        $prepared_stmt->execute($albums);
        $result_threads = $prepared_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (in_array('message', $fields)) {
        // search in threads messages and comments

        $where = $where_base;
        if (count($words) > 0) {
            $where .= ' AND ( ';
            foreach ($words as $index => $word) {
                if ($index > 0) {
                    $where .= ' OR ';
                }
                $where .= 'c.message LIKE ' . $db_object->quote("%" . $word . "%") . ' ';
            }
            $where .= ') ';
        }

        /*
         * SELECT DISTINCT ... FROM threads
         * WHERE albumName IN ( ' ... ' )  <-- selection of albums
         * AND assetName LIKE '%$asset%' <-- if $asset != ''
         * AND studentOnly == 0 <-- if user is a teacher
         * AND deleted != 1
         * AND (message LIKE '%$word[i]%' OR message LIKE '%word[i+1]%' ...)
         * GROUP BY albumName, assetName, id;
         */

        $stmt = 'SELECT DISTINCT id, title, message, timecode, albumName, assetName, assetTitle, studentOnly '
                . 'FROM ' . db_gettable('threads') . ' c '
                . $where
                . 'GROUP BY albumName, assetName, id';

        $prepared_stmt = $db_object->prepare($stmt);
        $prepared_stmt->execute($albums);
        $result_messages = $prepared_stmt->fetchAll(PDO::FETCH_ASSOC);

        /*
         * SELECT DISTINCT ... FROM comments c
         * JOIN threads t ON ...
         * WHERE albumName IN ( ' ... ' )  <-- selection of albums
         * AND assetName LIKE '%$asset%' <-- if $asset != ''
         * AND studentOnly == 0 <-- if user is a teacher
         * AND c.deleted != 1
         * AND (c.message LIKE '%$word[i]%' OR c.message LIKE '%word[i+1]%' ...)
         * AND t.deleted != 1
         * GROUP BY albumName, assetName, id;
         */

        $stmt = 'SELECT DISTINCT thread, title, c.message, t.message as thread_message, timecode, albumName, assetName, assetTitle, c.id, studentOnly ' .
                'FROM ' . db_gettable('comments') . ' c ' .
                'JOIN ' . db_gettable('threads') . ' t on c.thread = t.id ' .
                $where . ' AND t.deleted = 0 ' .
                'GROUP BY albumName, assetName, t.id, c.id';

        $prepared_stmt = $db_object->prepare($stmt);
        $prepared_stmt->execute($albums);
        $result_comments = $prepared_stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // we have now 1/2/3 arrays (depending on fields 'title' and 'message')
    // $result_threads contains all threads where one of the words is in the title
    // $result_messages contains all threads where one of the words is in the message
    // $result_comments contains all the comments where one of the words is in the message

    $threads_array = array();

    // loop on threads that contain at least one word in the title
    foreach ($result_threads as $thread) {
        $score = 0;

        if ($relevancy) {
            foreach ($words as $word) {
                // search the word in the title
                $offset = stripos($thread['title'], $word);
                if ($offset !== false) {
                    // the word has been found, we increment the score
                    $last_index = $offset + strlen($word);
                    $score++;

                    // there is nothing before and/or after the word, we increment the score
                    if ($offset == 0) {
                        $score++;
                    }
                    if ($last_index == strlen($thread['title'])) {
                        $score++;
                    }
                    if ($offset > 0 && $thread['title'][$offset - 1] == ' ') {
                        $score++;
                    }
                    if ($last_index < strlen($thread['title']) && $thread['title'][$last_index] == ' ') {
                        $score++;
                    }

                    // There are multiple occurences of the word, we increment the score
                    $count = substr_count(strtoupper($thread['title']), strtoupper($word));
                    if ($count > 1) {
                        $score += ($count - 1) * 2;
                    }
                }
            }
            $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['score'] = $score;
        }
        $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['title'] = $thread['title'];
        $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['message'] = $thread['message'];
        $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['timecode'] = $thread['timecode'];
        $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['comments'] = array();
    }

    // loop on thread that contain at least one word in message
    foreach ($result_messages as $thread) {
        $score = 0;
        if ($relevancy) {
            foreach ($words as $word) {
                // search the word in the message
                $offset = stripos($thread['message'], $word);
                if ($offset !== false) {
                    // the word has been found, we increment the score
                    $last_index = $offset + strlen($word);
                    $score++;

                    // there is nothing before and/or after the word, we increment the score
                    if ($offset == 0) {
                        $score++;
                    }
                    if ($last_index == strlen($thread['message'])) {
                        $score++;
                    }
                    if ($offset > 0 && $thread['message'][$offset - 1] == ' ') {
                        $score++;
                    }
                    if ($last_index < strlen($thread['message']) && $thread['message'][$last_index] == ' ') {
                        $score++;
                    }

                    // There are multiple occurences of the word, we increment the score
                    $count = substr_count(strtoupper($thread['message']), strtoupper($word));
                    if ($count > 1) {
                        $score += ($count - 1) * 2;
                    }
                }
            }
        }

        if (isset($threads_array[$thread['albumName']][$thread['assetName']][$thread['id']])) {
            // updates the score of the thread
            if ($relevancy) {
                $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['score'] += $score;
            }
        } else {
            if ($relevancy) {
                $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['score'] = $score;
            }
            $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['title'] = $thread['title'];
            $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['message'] = $thread['message'];
            $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['timecode'] = $thread['timecode'];
            $threads_array[$thread['albumName']][$thread['assetName']][$thread['id']]['comments'] = array();
        }
    }

    // loop on comments that contain at least one word
    foreach ($result_comments as $thread_comment) {
        $score = 0;
        if ($relevancy) {
            foreach ($words as $word) {
                // search the word in the message
                $offset = stripos($thread_comment['message'], $word);
                if ($offset !== false) {
                    // the word has been found, we increment the score
                    $last_index = $offset + strlen($word);
                    $score++;

                    // there is nothing before and/or after the word, we increment the score
                    if ($offset == 0) {
                        $score++;
                    }
                    if ($last_index == strlen($thread_comment['message'])) {
                        $score++;
                    }
                    if ($offset > 0 && $thread_comment['message'][$offset - 1] == ' ') {
                        $score++;
                    }
                    if ($last_index < strlen($thread_comment['message']) && $thread_comment['message'][$last_index] == ' ') {
                        $score++;
                    }

                    // There are multiple occurences of the word, we increment the score
                    $count = substr_count(strtoupper($thread_comment['message']), strtoupper($word));
                    if ($count > 1) {
                        $score += ($count - 1) * 2;
                    }
                }
            }
        }

        // the thread is already in the list
        if (isset($threads_array[$thread_comment['albumName']][$thread_comment['assetName']][$thread_comment['thread']])) {
            // updates the score of the thread
            if ($relevancy) {
                $threads_array[$thread_comment['albumName']][$thread_comment['assetName']][$thread_comment['thread']]['score'] += $score;
            }
            $threads_array[$thread_comment['albumName']][$thread_comment['assetName']][$thread_comment['thread']]['comments'][$thread_comment['id']] = $thread_comment['message'];
        } else {
            if ($relevancy) {
                $threads_array[$thread_comment['albumName']][$thread_comment['assetName']][$thread_comment['thread']]['score'] = $score;
            }
            $threads_array[$thread_comment['albumName']][$thread_comment['assetName']][$thread_comment['thread']]['title'] = $thread_comment['title'];
            $threads_array[$thread_comment['albumName']][$thread_comment['assetName']][$thread_comment['thread']]['message'] = $thread_comment['thread_message'];
            $threads_array[$thread_comment['albumName']][$thread_comment['assetName']][$thread_comment['thread']]['timecode'] = $thread_comment['timecode'];
            $threads_array[$thread_comment['albumName']][$thread_comment['assetName']][$thread_comment['thread']]['comments'][$thread_comment['id']] = $thread_comment['message'];
        }
    }

    return $threads_array;
}

function thread_search_old($words, $fields, $albums, $asset = '')
{
    global $db_object;

    if (count($fields) <= 0) {
        return null;
    }
    if (count($albums) <= 0) {
        return null;
    }

    // db columns where we can do a search
    $accepted_fields = array(
        'title' => array('title', 't.title'),
        'message' => array('message', 'c.message')
    );

    // prepares the fields to add in the query
    foreach ($fields as $field) {
        foreach ($accepted_fields as $accepted_field => $val) {
            if (strpos($field, $accepted_field) !== false) {
                $fields_in_query_thread[] = $val[0];
                $fields_in_query_comment[] = $val[1];
                break;
            }
        }
    }

    // for each album in the array, adds a parameter in the prepared statement
    $albums_in_query = implode(',', array_fill(0, count($albums), '?'));

    switch (count($words)) {
        case 0:
            $where = '';
            // stmt will be:
            // SELECT ... FROM threads
            break;
        case 1:
            $where = '';
            foreach ($fields_in_query_thread as $field => $column) {
                $where .= ($where == '') ? 'WHERE ' : ' OR ';
                $where .= $column . ' LIKE ' . $db_object->quote("%" . $words[0] . "%");
            }
            // stmt will be:
            // SELECT ... FROM threads
            // WHERE title LIKE "%$word%"
            // OR message LIKE "%$word%"
            break;
        default:
            $where = '';
            foreach ($words as $index => $word) {
                if ($index == 0) {
                    $where .= 'WHERE ( ';
                } else {
                    $where .= ' AND id IN (SELECT id FROM ' . db_gettable('threads') . ' WHERE ';
                }
                $or = '';
                foreach ($fields_in_query_thread as $field => $column) {
                    $where .= $or . $column . ' LIKE ' . $db_object->quote("%" . $word . "%");
                    $or = ' OR ';
                }
                $where .= ') ';
            }
        // stmt will be:
        // SELECT ... FROM threads
        // WHERE (title LIKE '%$words[i]%' OR message LIKE '%$words[i]%')
        // AND id IN (SELECT id FROM threads WHERE title LIKE '%$words[i+1]%' OR message LIKE '%$words[i+1]%')
        // AND id IN (SELECT id FROM threads WHERE title LIKE '%$words[i+2]%' OR message LIKE '%$words[i+2]%')
        // AND ...
    }

    $stmt = 'SELECT DISTINCT id, title, message, timecode, albumName, assetName, assetTitle, studentOnly '
            . 'FROM ' . db_gettable('threads') . ' '
            . $where
            . (($where == '') ? ' WHERE ' : ' AND ')
            . 'albumName in(' . $albums_in_query . ') '
            . ((isset($asset) && $asset != '') ? ' AND assetName LIKE ' . $db_object->quote("%$asset%") . ' ' : ' ')
            . 'GROUP BY albumName, assetName, id';

    $prepared_stmt = $db_object->prepare($stmt);
    $prepared_stmt->execute($albums);
    $result_threads = $prepared_stmt->fetchAll();

    $stmt = 'SELECT DISTINCT t.id, t.title, c.message, t.albumName, t.assetTitle, t.studentOnly '
            . 'FROM ' . db_gettable('comments') . ' c '
            . 'JOIN threads t on t.id = thread '
            . 'WHERE MATCH(' . $fields_in_query_comment . ') '
            . 'AGAINST(' . $quoted_search . ' IN BOOLEAN MODE) '
            . 'AND t.albumName in(' . $albums_in_query . ') '
            . ((isset($asset) && $asset != '') ? 'AND assetName LIKE ' . $db_object->quote("%$asset%") . ' ' : ' ')
            . 'GROUP BY t.albumName, t.assetName, t.id, c.id';
    return $result_threads;

    /*
      // --> Alternatively, we can use MATCH(...) AGAINST(...) but
      //     it doesn't give expected results.
     */

    // converts fields array in string to inject in the prepared query
    // we can't use db->quote() or bind param because it adds quotes
    // and we can't have quotes in SQL 'MATCH' instruction
    $fields_in_query_thread = implode(',', $fields_in_query_thread);
    $fields_in_query_comment = implode(',', $fields_in_query_comment);

    // for each album in the array, adds a parameter in the prepared statement
    $albums_in_query = implode(',', array_fill(0, count($albums), '?'));

    // for each word to search, adds the word in a string
    // for SQL 'AGAINST' instruction
    foreach ($words as $word) {
        $search .= '+' . $word . '* ';
    }

    // pdo quotes the string to search, to avoid SQL injections
    $quoted_search = $db_object->quote($search);

    // prepared stmt
    $stmt = 'SELECT DISTINCT id, title, message, timecode, albumName, assetName, assetTitle, studentOnly '
            . 'FROM ' . db_gettable('threads')
            . ' WHERE MATCH(' . $fields_in_query_thread . ') '
            . 'AGAINST(' . $quoted_search . ' IN BOOLEAN MODE) '
            . 'AND albumName in(' . $albums_in_query . ') '
            . ((isset($asset) && $asset != '') ? 'AND assetName LIKE ' . $db_object->quote("%$asset%") . ' ' : ' ')
            . 'GROUP BY albumName, assetName, id';

    $prepared_stmt = $db_object->prepare($stmt);
    $prepared_stmt->execute($albums);
    $result_threads = $prepared_stmt->fetchAll();

    $stmt = 'SELECT DISTINCT t.id, t.title, c.message, t.albumName, t.assetTitle, t.studentOnly '
            . 'FROM ' . db_gettable('comments') . ' c '
            . 'JOIN threads t on t.id = thread '
            . 'WHERE MATCH(' . $fields_in_query_comment . ') '
            . 'AGAINST(' . $quoted_search . ' IN BOOLEAN MODE) '
            . 'AND t.albumName in(' . $albums_in_query . ') '
            . ((isset($asset) && $asset != '') ? 'AND assetName LIKE ' . $db_object->quote("%$asset%") . ' ' : ' ')
            . 'GROUP BY t.albumName, t.assetName, t.id, c.id';

    $prepared_stmt = $db_object->prepare($stmt);
    $prepared_stmt->execute($albums);
    $result_comments = $prepared_stmt->fetchAll();

    return array_merge($result_threads, $result_comments);
}

/**
 * Search and returns threads witch contains the words
 * @global type $db
 * @param type $words a vcs string
 */
/* function thread_search($words, $target, $albums, $fields) {
  global $statements;
  $album_csv = '';
  if (sizeof($albums) > 0) {
  $album_csv = "\"" . $albums[0] . "\"";
  foreach ($albums as $value) {
  $album_csv .= ",\"" . $value . "\"";
  }
  } else {
  return null;
  }
  $size = sizeof($fields);
  if ($size > 0) {
  $fields_csv = ($size == 1) ? $fields[0] : $fields[0] . ',' . $fields[1];
  } else {
  return null;
  }
  // QUERY THREADS TABLE
  $statements['search_thread']->bindParam(':fields_csv', $fields_csv, PDO::PARAM_STR);
  $statements['search_thread']->bindParam(':words', $words, PDO::PARAM_STR);
  $statements['search_thread']->bindParam(':album_csv', $album_csv, PDO::PARAM_STR);
  $res_thread = $statements['search_thread']->execute();
  echo $fields_csv . "<br/>";
  echo $words . "<br/>";
  echo $album_csv . "<br/>";
  //   var_dump($statements['search_thread']);die;
  $result_thread = $statements['search_thread']->fetchAll();

  //  var_dump($statements['search_thread']->debugDumpParams());die;
  var_dump($statements['search_thread']->errorInfo());die;

  //    var_dump($result_thread);die;

  // QUERY COMMENTS TABLE
  $fields[0] = (($fields[0] == "title") ? 't.title' : 'c.message');
  if ($size > 1)
  $fields[1] = (($fields[1] == "title") ? 't.title' : 'c.message');

  $fields_comment_csv = ($size == 1) ? $fields[0] : $fields[0] . ',' . $fields[1];

  $statements['search_comment']->bindParam(':fields_csv', $fields_comment_csv);
  $statements['search_comment']->bindParam(':words', $words);
  $statements['search_comment']->bindParam(':album_csv', $album_csv);
  $statements['search_comment']->execute();
  $result_comment = $statements['search_comment']->fetchAll();


  // MERGE THE 2 RESULTS
  return array_merge($result_thread, $result_comment);
  }

 */

// ===== C O M M E N T S
/**
 * Inserts into the database a new comment
 * @global type $db
 * @param type $values
 * @return boolean errorflag
 */
function comment_insert($values)
{
    global $statements;

    //    foreach ($values as $row => $value) {
    //        $statements['comment_insert']->bindParam(':'.$row, $value);
    //    }
    if (!isset($values['parent'])) {
        $statements['comment_insert']->bindParam(':message', $values['message']);
        $statements['comment_insert']->bindParam(':thread', $values['thread']);
        $statements['comment_insert']->bindParam(':authorId', $values['authorId']);
        $statements['comment_insert']->bindParam(':authorFullName', $values['authorFullName']);
        $statements['comment_insert']->bindParam(':creationDate', $values['creationDate']);
        $statements['comment_insert']->bindParam(':lastEditDate', $values['lastEditDate']);

        $res = $statements['comment_insert']->execute();
    } else {
        $statements['comment_insert_reply']->bindParam(':message', $values['message']);
        $statements['comment_insert_reply']->bindParam(':thread', $values['thread']);
        $statements['comment_insert_reply']->bindParam(':authorId', $values['authorId']);
        $statements['comment_insert_reply']->bindParam(':authorFullName', $values['authorFullName']);
        $statements['comment_insert_reply']->bindParam(':creationDate', $values['creationDate']);
        $statements['comment_insert_reply']->bindParam(':lastEditDate', $values['lastEditDate']);
        $statements['comment_insert_reply']->bindParam(':parent', $values['parent']);
        $res = $statements['comment_insert_reply']->execute();
    }

    if ($res) {
        thread_update_nbComments($values['thread'], true);
        if (isset($values['parent'])) {
            comment_update_nbChild($values['parent'], true);
        }
    }

    thread_update_lastEdit($values['thread'], $values['authorFullName']);
    cache_album_threads_unset($album);
    log_append('Create comment: ', 'Create comment with title = ' . $values['title']);

    return $res;
}

/**
 * Updates the message of a comment
 * @global null $db
 * @param type $id
 * @param type $message
 * @param string $album
 * @param string $asset
 * @param string $thread
 *
 * @return boolean true if succed
 */
function comment_update($id, $message, $album, $asset, $thread, $author = 'Unknown')
{
    global $statements;
    $date = date('Y-m-d H:i:s');
    $statements['comment_update']->bindParam(':message', $message);
    $statements['comment_update']->bindParam(':lastEditDate', $date);
    $statements['comment_update']->bindParam(':id', $id);

    $res = $statements['comment_update']->execute();
    thread_update_lastEdit($thread, $author);
    cache_album_threads_unset($album);
    log_append('Edit comment: ', 'Edit comment with id = ' . $id);
    return $res;
}

/**
 * Updates the number of children of a comment
 * @global null $db
 * @param type $_id
 * @param boolean $up true to add one and false to remove one
 * @return boolean
 */
function comment_update_nbChild($_id, $up)
{
    global $statements;
    if ($up) {
        $statements['comment_update_nbChild_up']->bindParam(':id', $_id);
        $res = $statements['comment_update_nbChild_up']->execute();
    } else {
        $statements['comment_update_nbChild_down']->bindParam(':id', $_id);
        $res = $statements['comment_update_nbChild_down']->execute();
    }
    return $res;
}

/**
 * Retrieve a comment from the database and returns an associative array with his values
 * @global type $db
 * @param int $_id
 * @return array
 */
function comment_select_by_id($id)
{
    global $statements;

    $statements['comment_select_by_id']->bindParam(':id', $id);
    $statements['comment_select_by_id']->execute();
    $res = $statements['comment_select_by_id']->fetchAll(PDO::FETCH_ASSOC);
    return $res[0];
}

/**
 * Returns an array with the comments posted on the current thread
 * @global type $db
 * @global int $current_thread
 * @return array
 */
function comment_select_by_thread($thread_id = '')
{
    global $statements;
    if ($thread_id == '') {
        $thread_id = $_SESSION['current_thread'];
    }
    $statements['comment_select_by_threadId']->bindParam(':thread', $thread_id);
    $statements['comment_select_by_threadId']->execute();
    $res = $statements['comment_select_by_threadId']->fetchAll(PDO::FETCH_ASSOC);
    return $res;
}

/**
 * Returns an array containing all childs of the given comment
 * @param type $id
 */
function comment_children_get($id)
{
    global $statements;

    $statements['comment_children_get']->bindParam(':parent', $id);
    $statements['comment_children_get']->execute();
    $res = $statements['comment_children_get']->fetchAll(PDO::FETCH_ASSOC);
    return $res;
}

/**
 * Deletes a comment using his ID
 * @global null $db
 * @param int $id
 * @return boolean error flag
 */
function comment_delete_by_id($id)
{
    global $statements;

    $comment = comment_select_by_id($id);
    $statements['comment_delete_by_id']->bindParam(':id', $id);
    $res = $statements['comment_delete_by_id']->execute();
    if ($res) {
        thread_update_nbComments($comment['thread'], false);
        if ($comment['nbChilds'] > 0) {
            $children = comment_children_get($id);
            foreach ($children as $child) {
                comment_delete_by_id($child['id']);
            }
        }
        if ($comment['parent'] != null) {
            comment_update_nbChild($comment['parent'], false);
        }
    }


    log_append('Delete comment: ', 'Delete comment with id = ' . $id);
    return $res;
}

/**
 * Delete all comments posted on a thread
 * @global type $db
 * @param int $_id
 * @return boolean errorflag
 */
function comment_delete_by_thread($thread_id = '')
{
    global $statements;
    if ($thread_id == '') {
        $thread_id = $_SESSION['current_thread'];
    }
    $statements['comment_delete_by_thread']->bindParam(':thread', $thread_id);
    $res = $statements['comment_delete_by_thread']->execute();
    thread_reinit_nbComments($thread_id);
    return $res;
}

/**
 * Set or unset the approval attibute
 * @global type $db
 * @param int $_comId
 * @return boolean errorflag
 */
function comment_update_approval($_comId)
{
    global $statements;

    $comment = comment_select_by_id($_comId);
    $approval = ($comment['approval'] == '0') ? '1' : '0';
    $statements['comment_update_approval']->bindParam(':approval', $approval);
    $statements['comment_update_approval']->bindParam(':id', $_comId);

    return $statements['comment_update_approval']->execute();
}

function comment_approval_remove($comment_id)
{
    global $statements;
        
    $approval = '0';
    $statements['comment_update_approval']->bindParam(':approval', $approval);
    $statements['comment_update_approval']->bindParam(':id', $comment_id);

    return $statements['comment_update_approval']->execute();
}

/**
 * Returns the best answer of the corrent thread
 * @global type $db
 * @global int $current_thread
 * @return array
 */
function comment_select_best($thread_id = '')
{
    global $statements;
    if (!$thread_id) {
        $thread_id = $_SESSION['current_thread'];
    }
    $statements['comment_select_best']->bindParam(':thread', $thread_id);
    $statements['comment_select_best']->execute();
    $res = $statements['comment_select_best']->fetch(PDO::FETCH_ASSOC);

    return $res;
}

// ===== V O T E
/**
 * Inserts a new vote into the database
 * @global type $db
 * @param type $values
 * @return type
 */
function vote_insert($values)
{
    global $statements;
    
    
    $statements['vote_user_get']->bindParam(":login", $values['login']);
    $statements['vote_user_get']->bindParam(":comment", $values['comment']);
    $statements['vote_user_get']->execute();
    $votePlayer = $statements['vote_user_get']->fetch();
    $double = false;
    
    // If player already vote and vote same that now
    if ($votePlayer['row'] > 0 && $votePlayer['voteType'] == $values['voteType']) {
        $values['voteType'] = -$values['voteType'];
        
        $res = 0;
        // cancel vote
        $statements['vote_cancel']->bindParam(":login", $values['login']);
        $statements['vote_cancel']->bindParam(":comment", $values['comment']);
        $statements['vote_cancel']->execute();
    } else { // else
        // Change the vote table with the new value
        $statements['vote_insert']->bindParam(":login", $values['login']);
        $statements['vote_insert']->bindParam(":comment", $values['comment']);
        $statements['vote_insert']->bindParam(":voteType", $values['voteType']);
        $statements['vote_insert']->execute();
        $res = $values['voteType'];
        
        // If already vote, double the value
        if ($votePlayer['row'] > 0) {
            $double = true;
        }
    }
    comment_update_score($values['comment'], $values['voteType'] == '1', $double);
    
    return $res;
}

/**
 * Deletes all votes for a given comment
 * @global type $statements
 * @param type $comment_id
 * @return type
 */
function vote_delete($comment_id)
{
    global $statements;

    $statements['vote_delete']->bindParam(":comment", $comment_id);
    $res = $statements['vote_delete']->execute();
    if ($res) {
        comment_score_init($comment_id);
    }
    return $res;
}

/**
 * Reinit the score of a given comment
 * @global type $statements
 * @param type $comment_id
 * @return type
 */
function comment_score_init($comment_id)
{
    global $statements;

    $statements['comment_score_init']->bindParam(':id', $comment_id);
    $res = $statements['comment_score_init']->execute();

    return $res;
}

function comment_update_score($_id, $up, $double = false)
{
    global $statements;
    
    $strStatement = 'comment_update_score_';
    if ($double) {
        $strStatement .= 'double_';
    }
    
    if ($up) {
        $strStatement.= 'up';
    } else {
        $strStatement.= 'down';
    }
    $statements[$strStatement]->bindParam(':id', $_id);
    $res = $statements[$strStatement]->execute();
    
    return $res;
}

// ===== U T I L I T I E S =====================================================
/**
 * Defines the current thread
 * @global int $current_thread
 * @param int $_id
 */
function thread_set_current_thread($_id)
{
    global $current_thread;
    $current_thread = $_id;
    $_SESSION['current_thread'] = $_id;
}
