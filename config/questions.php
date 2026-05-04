<?php

return [
    [
        'id' => 1,
        'type' => 'qcm',
        'title' => 'Question 1',
        'question' => 'Quelle est la capitale de la France ?',
        'options' => [
            'a' => 'Berlin',
            'b' => 'Madrid',
            'c' => 'Paris',
            'd' => 'Rome',
        ],
        'correct_answer' => 'c',
    ],
    [
        'id' => 2,
        'type' => 'qcm',
        'title' => 'Question 2',
        'question' => 'Combien font 2 + 2 ?',
        'options' => [
            'a' => '3',
            'b' => '4',
            'c' => '5',
            'd' => '6',
        ],
        'correct_answer' => 'b',
    ],
    [
        'id' => 3,
        'type' => 'qcm',
        'title' => 'Question 3',
        'question' => 'Quel mot-clé SQL permet de récupérer des données ?',
        'options' => [
            'a' => 'INSERT',
            'b' => 'UPDATE',
            'c' => 'DELETE',
            'd' => 'SELECT',
        ],
        'correct_answer' => 'd',
    ],
    [
        'id' => 4,
        'type' => 'qcm',
        'title' => 'Question 4',
        'question' => 'Quel type de test vérifie le bon fonctionnement global d’une fonctionnalité côté utilisateur ?',
        'options' => [
            'a' => 'Test unitaire',
            'b' => 'Test fonctionnel',
            'c' => 'Test de charge',
            'd' => 'Test réseau',
        ],
        'correct_answer' => 'b',
    ],
    [
        'id' => 5,
        'type' => 'text',
        'title' => 'Question 5',
        'question' => 'Écris uniquement le chiffre 1 dans la zone ci-dessous.',
        'accepted_answers' => ['1'],
        'correct_answer_display' => '1',
    ],
];