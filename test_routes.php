<?php

function call($method, $url, $data = [], $token = null) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $headers = ['Accept: application/json'];
    if ($token) $headers[] = "Authorization: Bearer $token";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    } elseif (in_array($method, ['PUT', 'DELETE'])) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$status, $response];
}

function login($email, $password, $baseUrl, $label) {
    [$status, $response] = call('POST', "$baseUrl/auth/login", [
        'email' => $email,
        'password' => $password
    ]);
    $json = json_decode($response, true);

    if(isset($json['error'])){
        echo "\n❌ Falha no login de $label: " . ($json['error'] ?? 'Erro desconhecido') . "\n";
        exit(1);
    }
    return $json['token'];
}

$laravelPath =  dirname(__DIR__).'/ClickBeard_Samuel_Paviotti';
$command = "cd $laravelPath && php artisan migrate:fresh --seed";

exec($command, $output, $returnCode);

if ($returnCode === 0) {
    echo "Comando executado com sucesso!\n";
    echo "Saída: " . implode("\n", $output);
} else {
    echo "Erro ao executar comando (Código: $returnCode)\n";
    echo "Erro: " . implode("\n", $output);
}


$baseUrl = 'http://localhost:8001';

echo "\n🔐 Login CLIENTE...\n";
$clientToken = login('cliente@barbearia.com', '1234', $baseUrl, 'cliente'); 

echo "🔐 Login ADMIN...\n";
$adminToken = login('admin@barbearia.com', '1234', $baseUrl, 'admin'); 

$routesClient = [
    ['GET', '/'],
    ['GET', '/barbers'],
    ['GET', '/barbers/1'],
    ['GET', '/services'],
    ['GET', '/specialties'],
    ['GET', '/auth/me'],
    ['GET', '/scheduling'],
    ['POST', '/scheduling', ['barber_id'=>1, 'service_id'=>1, 'scheduling_date'=>'2025-06-25', 'scheduling_time'=>'10:00']],
    ['GET', '/scheduling/1'],
    ['PUT', '/scheduling/1', ['notes' => 'Quero um corte moderno']],
    ['GET', '/available-slots', ['barber_id'=>1, 'date'=>'2025-06-23']],
    ['GET', '/barbers-by-specialty'],
    ['PUT', '/scheduling/1/confirm'],
    ['PUT', '/scheduling/1/complete'],
    ['PUT', '/scheduling/1/cancel'],
    ['POST', '/auth/logout'],
];

$routesAdmin = [
    ['GET', '/admin/scheduling/today'],
    ['GET', '/admin/scheduling/future'],
    ['GET', '/admin/scheduling/date/2025-06-22'],
    ['POST', '/admin/barbers', ['name'=>'Novo', 'email'=>'novo@teste.com', 'age'=>30, 'phone'=>'111', 'hire_date'=>'2025-01-01']],
    ['PUT', '/admin/barbers/1', ['name'=>'Atualizado']],
    ['POST', '/admin/barbers/1/specialties', ['specialty_id'=>1, 'experience_years'=>3, 'is_primary'=>true]],
    ['DELETE', '/admin/barbers/1/specialties'],
    ['POST', '/admin/specialties', ['name'=>'Nova', 'description'=>'desc', 'is_active'=>1]],
    ['PUT', '/admin/specialties/1', ['name'=>'Atualizada']],
    ['DELETE', '/admin/specialties/1'],
    ['POST', '/admin/services', ['name'=>'Novo serviço', 'description'=>'desc', 'specialty_id'=>1, 'price'=>30, 'duration_minutes'=>30]],
    ['PUT', '/admin/services/1', ['name'=>'Editado']],
    ['DELETE', '/admin/services/1'],
    ['POST', '/auth/logout'],
];

echo "\n🟢 TESTES COMO CLIENTE\n";
foreach ($routesClient as $route) {
    [$method, $uri] = $route;
    $data = $route[2] ?? [];
    [$status] = call($method, $baseUrl . $uri, $data, $clientToken);
    echo "[$method] $uri → HTTP $status\n";
}

echo "\n🔴 TESTES COMO ADMIN\n";
foreach ($routesAdmin as $route) {
    [$method, $uri] = $route;
    $data = $route[2] ?? [];
    [$status] = call($method, $baseUrl . $uri, $data, $adminToken);
    echo "[$method] $uri → HTTP $status\n";
}

