# lib-adb

## Instalasi

```
mim app install lib-adb
```

## Konfigurasi

Tambahkan konfirugasi seperti di bawah pada konfigurasi aplikasi:

```
return [
    'libAdb' => [
        'bin' => '/path/to/adb/binary'
    ]
];
```

## Classes

### LibAdb\Library\Adb

1. `->construct(string $port = '5037')`
2. `->detach(string $id): void`
4. `->devices(bool $long = false): array`
3. `->exec(string $command): ?string`
5. `->getName(string $id): ?string`
6. `->pair(string $address, string $code): ?string`
7. `->single(string $id): void`
8. `->stop()`
