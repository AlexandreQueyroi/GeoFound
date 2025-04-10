<?php
include_once(__DIR__ . '/bdd.php');
function getRankColorSpan($rank)
{
    global $conn;
    $query = $conn->prepare("SELECT name, color FROM ranks WHERE id = :rank");
    $query->execute(['rank' => $rank]);
    $result = $query->fetch();

    if ($result) {
        return "<span class=\"text-[#" . htmlspecialchars($result['color']) . "] p-1\">" . htmlspecialchars($result['name']) . "</span>";
    }
}


function getRankColorDiv($rank)
{
    global $conn;
    $query = $conn->prepare("SELECT color FROM ranks WHERE id = :rank");
    $query->execute(['rank' => $rank]);
    $result = $query->fetch();

    if ($result) {
        return "<div class=\"bg-[#" . htmlspecialchars($result['color']) . "] p-4;\">#" . htmlspecialchars($result['color']) . "</div>";
    }
}

function getRankName($rank)
{
    global $conn;
    $query = $conn->prepare("SELECT name FROM ranks WHERE id = :rank");
    $query->execute(['rank' => $rank]);
    $result = $query->fetch();

    if ($result) {
        return htmlspecialchars($result['name']);
    }
}

function getRankId($rank)
{
    global $conn;
    $query = $conn->prepare("SELECT id FROM ranks WHERE name = :rank");
    $query->execute(['rank' => $rank]);
    $result = $query->fetch();

    if ($result) {
        return htmlspecialchars($result['id']);
    }
}

function getRankPermission($rank)
{
    global $conn;
    $query = $conn->prepare("SELECT permission FROM permissions WHERE rank_id = :rank");
    $query->execute(['rank' => $rank]);
    $result = $query->fetch();

    if ($result) {
        return htmlspecialchars($result['permission']);
    }
}