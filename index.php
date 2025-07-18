<?php
declare(strict_types=1);

$year = 2025;
$month = 2;
$numberOfMonths = 4;

// Функция проверки выходного дня
function isWeekend(DateTime $date): bool {
    return (int)$date->format('N') >= 6; // 6-суббота, 7-воскресенье
}

// Основной форматтер для вывода дат
function createDateFormatter(): IntlDateFormatter {
    return new IntlDateFormatter(
        'ru_RU',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'Europe/Moscow',
        IntlDateFormatter::GREGORIAN,
        'd MMMM Y, EEEE'
    );
}

// Форматтер только для названий месяцев
function createMonthFormatter(): IntlDateFormatter {
    return new IntlDateFormatter(
        'ru_RU',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        'Europe/Moscow',
        IntlDateFormatter::GREGORIAN,
        'LLLL'
    );
}

function showWorkingDays(array $dates, DateTime $startDate): void {
    $formatter = createDateFormatter();
    $monthFormatter = createMonthFormatter();
    
    $workDayIndex = 0;
    $currentMonth = $startDate->format('m');
    $currentMonthName = mb_convert_case(
        $monthFormatter->format($startDate), 
        MB_CASE_TITLE, 
        'UTF-8'
    );
    
    echo "Название месяца: " . $currentMonthName . PHP_EOL;
    echo "Список дней месяца: " . PHP_EOL;
    
    for ($index = 0; $index < count($dates); $index++) {
        $date = $dates[$index];
        $month = $date->format('m');
        
        // При смене месяца
        if ($month !== $currentMonth) {
            $currentMonth = $month;
            $currentMonthName = mb_convert_case(
                $monthFormatter->format($date), 
                MB_CASE_TITLE, 
                'UTF-8'
            );
            echo PHP_EOL . "Название месяца: " . $currentMonthName . PHP_EOL;
            echo "Список дней месяца: " . PHP_EOL;
            
            // Сброс рабочего индекса при смене месяца
            if ($index > $workDayIndex) {
                $workDayIndex = $index;
            }
        }
        
        $dayStr = $formatter->format($date->getTimestamp());
        
        if ($index === 0) {
            echo $dayStr . " +" . PHP_EOL;
            $workDayIndex += 3;
            continue;
        }

        if ($index === $workDayIndex) {
            if (!isWeekend($date)) {
                echo $dayStr . " +" . PHP_EOL;
                $workDayIndex += 3;
            } else {
                // Ищем следующий понедельник (убрали проверку месяца)
                $foundMonday = false;
                for ($i = $index; $i < count($dates); $i++) {
                    if ((int)$dates[$i]->format('N') === 1) {
                        echo $formatter->format($dates[$i]->getTimestamp()) . " +" . PHP_EOL;
                        $workDayIndex = $i + 3;
                        $index = $i; // Перескакиваем на понедельник
                        $foundMonday = true;
                        break;
                    }
                    echo $formatter->format($dates[$i]->getTimestamp()) . PHP_EOL;
                }
                
                if (!$foundMonday) {
                    break; // Завершаем если понедельник не найден
                }
            }
        } else {
            echo $dayStr . PHP_EOL;
        }
    }
}

function getSchedule(int $year, int $month, int $numberOfMonths): void {
    $startDate = new DateTime("$year-$month-01");
    $endDate = (new DateTime("$year-$month-01"))
        ->add(new DateInterval("P{$numberOfMonths}M"));
    
    $dates = [];
    $currentDate = clone $startDate;
    
    while ($currentDate < $endDate) {
        $dates[] = clone $currentDate;
        $currentDate->add(new DateInterval('P1D'));
    }
    
    showWorkingDays($dates, $startDate);
}

// Запуск
getSchedule($year, $month, $numberOfMonths);