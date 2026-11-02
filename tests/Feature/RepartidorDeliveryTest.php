<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepartidorDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_repartidor_can_see_assigned_orders_and_delivery_data(): void
    {
        $courier = $this->courier();
        $order = $this->assignedOrder();

        $this->actingAs($courier)
            ->get(route('delivery.index'))
            ->assertOk()
            ->assertSee($order->code)
            ->assertSee($order->delivery_address);

        $this->actingAs($courier)
            ->get(route('delivery.show', $order))
            ->assertOk()
            ->assertSee($order->client->name)
            ->assertSee($order->client->phone);
    }

    public function test_repartidor_can_start_route_report_incident_and_confirm_delivery(): void
    {
        $courier = $this->courier();
        $order = $this->assignedOrder();

        $this->actingAs($courier)
            ->post(route('delivery.start', $order))
            ->assertRedirect();

        $order->refresh();
        $this->assertSame('en_camino', $order->status);
        $this->assertNotNull($order->fecha_despacho);

        $this->actingAs($courier)
            ->post(route('delivery.incident.store', $order), [
                'incidencia_nota' => 'El cliente no se encontraba en el domicilio.',
            ])
            ->assertRedirect(route('delivery.show', $order));

        $order->refresh();
        $this->assertSame('con_incidencia', $order->status);
        $this->assertSame('El cliente no se encontraba en el domicilio.', $order->incidencia_nota);

        $this->actingAs($courier)
            ->post(route('delivery.confirm.store', $order), [
                'codigo_confirmacion' => '482915',
                'notes' => 'Entrega reprogramada y recibida por el cliente.',
            ])
            ->assertRedirect(route('delivery.show', $order));

        $order->refresh();
        $this->assertSame('entregado', $order->status);
        $this->assertNotNull($order->fecha_entrega);
        $this->assertDatabaseHas('delivery_confirmations', ['sales_order_id' => $order->id]);
    }

    public function test_confirmation_code_must_match(): void
    {
        $courier = $this->courier();
        $order = $this->assignedOrder();

        $this->actingAs($courier)->post(route('delivery.start', $order));

        $this->actingAs($courier)
            ->from(route('delivery.confirm', $order))
            ->post(route('delivery.confirm.store', $order), [
                'codigo_confirmacion' => '000000',
            ])
            ->assertRedirect(route('delivery.confirm', $order))
            ->assertSessionHasErrors('codigo_confirmacion');

        $this->assertSame('en_camino', $order->fresh()->status);
    }

    public function test_repartidor_cannot_view_orders_assigned_to_someone_else(): void
    {
        $otherCourier = User::create([
            'name' => 'Otro Repartidor',
            'email' => 'otro.repartidor@ferreteria.com',
            'password' => 'password',
            'role' => UserRole::Repartidor,
            'active' => true,
        ]);

        $this->actingAs($otherCourier)
            ->get(route('delivery.show', $this->assignedOrder()))
            ->assertForbidden();
    }

    public function test_admin_can_assign_a_courier_and_generate_confirmation_code(): void
    {
        $admin = User::where('email', 'admin@ejj.pe')->firstOrFail();
        $courier = $this->courier();
        $order = SalesOrder::whereNull('repartidor_id')->whereNotIn('status', ['entregado', 'anulado'])->firstOrFail();

        $this->actingAs($admin)
            ->post(route('orders.courier', $order), ['repartidor_id' => $courier->id])
            ->assertRedirect();

        $order->refresh();
        $this->assertSame($courier->id, $order->repartidor_id);
        $this->assertNotNull($order->codigo_confirmacion);
    }

    private function courier(): User
    {
        return User::where('email', 'repartidor@ferreteria.com')->firstOrFail();
    }

    private function assignedOrder(): SalesOrder
    {
        return SalesOrder::where('repartidor_id', $this->courier()->id)->firstOrFail();
    }
}
