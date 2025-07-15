<?php
declare(strict_types=1);

$year = 2025;
$month = 7;

function isWeekend(string $day): bool {
    return str_contains($day, 'суббота') || str_contains($day, 'воскресенье');
}

function showWorkingDays(array $days): void {
    $workDayIndex = 0;
    
    foreach ($days as $index => $day) {
        if ($index === $workDayIndex) {
            if (!isWeekend($day)) {
                echo $day . " +" . PHP_EOL;
                $workDayIndex += 3;
            } else {
                echo $day . PHP_EOL;
                
                for ($i = $index + 1; $i < count($days); $i++) {
                    if (str_contains($days[$i], 'понедельник')) {
                        $workDayIndex = $i;
                        break;
                    };
                };
            };
        } else {
            echo $day . PHP_EOL;
        };
    };
};

function getSchedule(int $year, int $month) : void {
    $date = new DateTime("$year-$month-01");
    $formatter = new IntlDateFormatter(
        'ru_RU',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'Europe/Moscow',
        IntlDateFormatter::GREGORIAN,
        'LLLL'
    );

    $listFormatter = new IntlDateFormatter(
        'ru_RU',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'Europe/Moscow',
        IntlDateFormatter::GREGORIAN,
        'd MMMM Y, EEEE'
    );

    $daysInMonth = $date->format('t');
    $listOfDays = [];
    $currentDate = clone $date;

    for ($i = 1; $i <= $daysInMonth; $i++) {
        $listOfDays[] = $listFormatter->format($currentDate);
        $currentDate->add(new DateInterval('P1D'));
    };
    
    $russianMonth = mb_convert_case($formatter->format($date), MB_CASE_TITLE, 'UTF-8');

    echo "Название месяца: " . $russianMonth . PHP_EOL;
    echo "Список дней месяца: " . PHP_EOL;
    showWorkingDays($listOfDays);
};

getSchedule($year, $month);