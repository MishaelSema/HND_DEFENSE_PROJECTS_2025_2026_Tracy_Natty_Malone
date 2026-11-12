<?php

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('users', function ($table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->enum('role', ['buyer', 'seller', 'admin'])->default('buyer');
    $table->string('phone')->nullable();
    $table->text('address')->nullable();
    $table->string('avatar')->nullable();
    $table->boolean('is_verified')->default(false);
    $table->timestamps();
});

Capsule::schema()->create('products', function ($table) {
    $table->id();
    $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->string('category');
    $table->integer('stock')->default(0);
    $table->enum('status', ['active', 'inactive', 'out_of_stock'])->default('active');
    $table->string('image')->nullable();
    $table->timestamps();
});

Capsule::schema()->create('orders', function ($table) {
    $table->id();
    $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
    $table->string('order_number')->unique();
    $table->decimal('total', 10, 2);
    $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
    $table->text('shipping_address')->nullable();
    $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
    $table->string('payment_method')->nullable();
    $table->timestamps();
});

Capsule::schema()->create('order_items', function ($table) {
    $table->id();
    $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
    $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
    $table->integer('quantity')->default(1);
    $table->decimal('price', 10, 2);
    $table->timestamps();
});

Capsule::schema()->create('categories', function ($table) {
    $table->id();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->timestamps();
});

echo "✅ Database tables created successfully!";
