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

1. `:exec(string $command): ?string`
2. `:devices(bool $long = false): array`
3. `:getName(string $id): ?string`
