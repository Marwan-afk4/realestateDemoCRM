<?php

use App\Support\PhoneNumber;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable()->index();
            $table->string('phone_e164')->nullable()->unique();
            $table->string('phone_secondary')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('whatsapp')->nullable();
            $table->string('national_id')->nullable();
            $table->string('source')->default('other')->index();
            $table->decimal('budget_min', 14, 2)->nullable();
            $table->decimal('budget_max', 14, 2)->nullable();
            $table->string('preferred_area')->nullable();
            $table->foreignId('uptown_type_id')->nullable()->constrained('uptown_types')->nullOnDelete();
            $table->string('intent')->nullable();
            $table->string('payment_preference')->nullable();
            $table->json('tags')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('last_contacted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pipeline_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('brocker_id')->nullable()->constrained('brockers')->nullOnDelete();
            $table->string('type')->index();
            $table->string('ticketable_type');
            $table->unsignedBigInteger('ticketable_id');
            $table->string('stage')->default('new')->index();
            $table->string('lost_reason')->nullable();
            $table->text('lost_note')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedTinyInteger('probability')->default(10);
            $table->timestamp('stage_changed_at')->nullable();
            $table->foreignId('stage_changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['ticketable_type', 'ticketable_id']);
            $table->index(['stage', 'owner_id']);
        });

        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pipeline_ticket_id')->nullable()->constrained('pipeline_tickets')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->index();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pipeline_ticket_id')->nullable()->constrained('pipeline_tickets')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('other');
            $table->string('title');
            $table->text('body')->nullable();
            $table->timestamp('due_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamps();
        });

        Schema::create('message_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('channel');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('crm_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('channel');
            $table->foreignId('message_template_id')->nullable()->constrained('message_templates')->nullOnDelete();
            $table->text('body');
            $table->json('filters')->nullable();
            $table->unsignedInteger('recipient_count')->default(0);
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        if (! Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->after('id')->constrained('contacts')->nullOnDelete();
        });

        Schema::table('sell_requests', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->after('user_id')->constrained('contacts')->nullOnDelete();
        });

        Schema::table('buy_appartment_installments', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->after('user_id')->constrained('contacts')->nullOnDelete();
        });

        Schema::table('deals', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->after('id')->constrained('contacts')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->after('contact_id')->constrained('leads')->nullOnDelete();
            $table->foreignId('pipeline_ticket_id')->nullable()->after('lead_id')->constrained('pipeline_tickets')->nullOnDelete();
            $table->foreignId('brocker_id')->nullable()->after('pipeline_ticket_id')->constrained('brockers')->nullOnDelete();
            $table->foreignId('uptown_id')->nullable()->after('uptown_type_id')->constrained('uptowns')->nullOnDelete();
            $table->decimal('value', 14, 2)->nullable()->after('number_of_units');
            $table->date('close_date')->nullable()->after('value');
            $table->unsignedTinyInteger('probability')->default(10)->after('close_date');
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->foreignId('deal_id')->nullable()->after('id')->constrained('deals')->nullOnDelete();
            $table->decimal('percentage', 8, 2)->nullable()->after('commission');
            $table->decimal('amount', 14, 2)->nullable()->after('percentage');
        });

        Schema::table('uptowns', function (Blueprint $table) {
            $table->foreignId('reserved_deal_id')->nullable()->after('status')->constrained('deals')->nullOnDelete();
        });

        Schema::table('brockers', function (Blueprint $table) {
            $table->foreignId('team_lead_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
        });

        $this->backfill();
        $this->seedPermissions();
        $this->seedTemplates();
    }

    public function down(): void
    {
        Schema::table('brockers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_lead_id');
        });
        Schema::table('uptowns', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reserved_deal_id');
        });
        Schema::table('commissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('deal_id');
            $table->dropColumn(['percentage', 'amount']);
        });
        Schema::table('deals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_id');
            $table->dropConstrainedForeignId('lead_id');
            $table->dropConstrainedForeignId('pipeline_ticket_id');
            $table->dropConstrainedForeignId('brocker_id');
            $table->dropConstrainedForeignId('uptown_id');
            $table->dropColumn(['value', 'close_date', 'probability']);
        });
        Schema::table('buy_appartment_installments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_id');
        });
        Schema::table('sell_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_id');
        });
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_id');
        });

        Schema::dropIfExists('crm_broadcasts');
        Schema::dropIfExists('message_templates');
        Schema::dropIfExists('crm_tasks');
        Schema::dropIfExists('crm_activities');
        Schema::dropIfExists('pipeline_tickets');
        Schema::dropIfExists('contacts');
    }

    private function backfill(): void
    {
        $now = now();

        $contactIdByE164 = [];

        $ensureContact = function (array $attrs) use (&$contactIdByE164, $now): ?int {
            $phone = trim((string) ($attrs['phone'] ?? ''));
            $e164 = $phone !== '' ? PhoneNumber::toE164($phone) : null;
            $key = $e164 ?: ('name:'.mb_strtolower((string) ($attrs['name'] ?? '')).'|'.($attrs['email'] ?? ''));

            if (isset($contactIdByE164[$key])) {
                return $contactIdByE164[$key];
            }

            if ($e164) {
                $existing = DB::table('contacts')->where('phone_e164', $e164)->value('id');
                if ($existing) {
                    $contactIdByE164[$key] = $existing;

                    return $existing;
                }
            }

            $id = DB::table('contacts')->insertGetId([
                'name' => $attrs['name'] ?: 'Unknown',
                'phone' => $phone ?: null,
                'phone_e164' => $e164 ?: null,
                'email' => $attrs['email'] ?? null,
                'whatsapp' => $e164 ?: ($phone ?: null),
                'source' => $attrs['source'] ?? 'other',
                'preferred_area' => $attrs['preferred_area'] ?? null,
                'owner_id' => $attrs['owner_id'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $contactIdByE164[$key] = $id;

            return $id;
        };

        $brokerUser = DB::table('brockers')->pluck('user_id', 'id');

        foreach (DB::table('leads')->orderBy('id')->cursor() as $lead) {
            $ownerId = $lead->brocker_id ? ($brokerUser[$lead->brocker_id] ?? null) : null;
            $contactId = $ensureContact([
                'name' => $lead->lead_name,
                'phone' => $lead->lead_phone,
                'source' => $lead->marketing_agency_id ? 'agency' : 'broker',
                'preferred_area' => $lead->interested_place ?? null,
                'owner_id' => $ownerId,
            ]);

            DB::table('leads')->where('id', $lead->id)->update(['contact_id' => $contactId]);

            $stage = match ($lead->status) {
                'done' => 'won',
                'lost' => 'lost',
                default => 'new',
            };

            $ticketId = DB::table('pipeline_tickets')->insertGetId([
                'contact_id' => $contactId,
                'owner_id' => $ownerId,
                'brocker_id' => $lead->brocker_id,
                'type' => 'lead',
                'ticketable_type' => \App\Models\Lead::class,
                'ticketable_id' => $lead->id,
                'stage' => $stage,
                'probability' => $stage === 'won' ? 100 : ($stage === 'lost' ? 0 : 10),
                'locked_at' => $ownerId ? $now : null,
                'locked_by' => $ownerId,
                'stage_changed_at' => $now,
                'created_at' => $lead->created_at ?? $now,
                'updated_at' => $now,
            ]);

            if ($lead->brocker_end_date) {
                DB::table('crm_tasks')->insert([
                    'contact_id' => $contactId,
                    'pipeline_ticket_id' => $ticketId,
                    'owner_id' => $ownerId,
                    'type' => 'assignment_expiry',
                    'title' => 'Assignment expiry',
                    'due_at' => $lead->brocker_end_date,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        foreach (DB::table('sell_requests')->orderBy('id')->cursor() as $request) {
            $user = $request->user_id ? DB::table('users')->find($request->user_id) : null;
            $contactId = $ensureContact([
                'name' => $user ? trim($user->first_name.' '.$user->last_name) : 'Seller',
                'phone' => $user->phone ?? null,
                'email' => $user->email ?? null,
                'source' => 'sell_request',
                'preferred_area' => $request->area ?? $request->city ?? null,
            ]);
            DB::table('sell_requests')->where('id', $request->id)->update(['contact_id' => $contactId]);

            $stage = match ($request->status) {
                'contacted' => 'contacted',
                'approved' => 'won',
                'rejected' => 'lost',
                default => 'new',
            };

            DB::table('pipeline_tickets')->insert([
                'contact_id' => $contactId,
                'type' => 'sell_request',
                'ticketable_type' => \App\Models\SellRequest::class,
                'ticketable_id' => $request->id,
                'stage' => $stage,
                'probability' => $stage === 'won' ? 100 : ($stage === 'lost' ? 0 : 10),
                'stage_changed_at' => $now,
                'created_at' => $request->created_at ?? $now,
                'updated_at' => $now,
            ]);
        }

        foreach (DB::table('buy_appartment_installments')->orderBy('id')->cursor() as $request) {
            $user = $request->user_id ? DB::table('users')->find($request->user_id) : null;
            $contactId = $ensureContact([
                'name' => $user ? trim($user->first_name.' '.$user->last_name) : 'Buyer',
                'phone' => $user->phone ?? null,
                'email' => $user->email ?? null,
                'source' => 'mortgage',
                'preferred_area' => $request->area ?? $request->city ?? null,
            ]);
            DB::table('buy_appartment_installments')->where('id', $request->id)->update(['contact_id' => $contactId]);

            $stage = match ($request->status) {
                'contacted' => 'contacted',
                'approved' => 'won',
                'rejected' => 'lost',
                default => 'new',
            };

            DB::table('pipeline_tickets')->insert([
                'contact_id' => $contactId,
                'type' => 'mortgage',
                'ticketable_type' => \App\Models\BuyAppartmentInstallment::class,
                'ticketable_id' => $request->id,
                'stage' => $stage,
                'probability' => $stage === 'won' ? 100 : ($stage === 'lost' ? 0 : 10),
                'stage_changed_at' => $now,
                'created_at' => $request->created_at ?? $now,
                'updated_at' => $now,
            ]);
        }

        foreach (DB::table('deals')->orderBy('id')->cursor() as $deal) {
            $contactId = $ensureContact([
                'name' => $deal->fullname,
                'phone' => $deal->phone,
                'email' => $deal->email,
                'source' => 'other',
            ]);
            $ticketId = $deal->lead_id
                ? DB::table('pipeline_tickets')->where('ticketable_type', \App\Models\Lead::class)->where('ticketable_id', $deal->lead_id)->value('id')
                : null;

            DB::table('deals')->where('id', $deal->id)->update([
                'contact_id' => $contactId,
                'pipeline_ticket_id' => $ticketId,
                'probability' => match ($deal->status) {
                    'approved' => 100,
                    'semidone' => 70,
                    'rejected' => 0,
                    default => 20,
                },
            ]);
        }
    }

    private function seedPermissions(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $names = [
            'view-contacts',
            'view-pipeline',
            'view-all-pipeline',
            'view-team-pipeline',
            'view-crm-tasks',
            'view-crm-reports',
            'view-message-templates',
            'view-leads',
        ];

        $permissions = [];
        foreach ($names as $name) {
            $permissions[] = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        Role::where('name', 'super-admin')->where('guard_name', 'web')->first()
            ?->givePermissionTo($permissions);
    }

    private function seedTemplates(): void
    {
        $now = now();
        DB::table('message_templates')->insert([
            [
                'name' => 'First WhatsApp follow-up',
                'channel' => 'whatsapp',
                'subject' => null,
                'body' => 'Hello {{name}}, this is {{agent}} from '.config('app.name').'. I wanted to follow up on the unit you asked about. When is a good time to talk?',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Viewing confirmation',
                'channel' => 'sms',
                'subject' => null,
                'body' => 'Hi {{name}}, your compound visit is confirmed. Please reply if you need to reschedule.',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Offer follow-up',
                'channel' => 'email',
                'subject' => 'Your unit offer',
                'body' => 'Dear {{name}},\n\nPlease find the offer we discussed. Reply to this email or WhatsApp us if you have questions.\n\n{{agent}}',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
};
