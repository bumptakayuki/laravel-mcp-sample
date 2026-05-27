<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Candidate;
use App\Models\Job;
use Illuminate\Database\Seeder;

class AtsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = collect([
            [
                'title' => 'バックエンドエンジニア',
                'department' => 'Product Development',
                'required_skills' => ['PHP', 'Laravel', 'MySQL'],
                'description' => 'API とドメインロジックを担当。Laravel 実務経験歓迎。',
            ],
            [
                'title' => 'フロントエンドエンジニア',
                'department' => 'Product Development',
                'required_skills' => ['Vue.js', 'TypeScript', 'Tailwind CSS'],
                'description' => 'SPA とデザインシステムの実装。',
            ],
            [
                'title' => 'フルスタックエンジニア',
                'department' => 'Product Development',
                'required_skills' => ['Laravel', 'Vue.js', 'AWS'],
                'description' => '小さなチームで E2E に関与。',
            ],
            [
                'title' => 'プロダクトマネージャー（PdM）',
                'department' => 'Product',
                'required_skills' => ['SaaS', 'HR Tech', 'Discovery'],
                'description' => '採用プロダクトのロードマップと要件定義。',
            ],
            [
                'title' => 'AIプロダクトエンジニア',
                'department' => 'AI Lab',
                'required_skills' => ['Python', 'LLM', 'API Design'],
                'description' => '社内ツールと MCP 連携のプロトタイプ。',
            ],
        ])->map(fn (array $row) => Job::query()->create($row));

        $profiles = [
            ['skills' => ['PHP', 'Laravel', 'MySQL', 'REST API'], 'position' => 'Backend Engineer', 'company' => 'HR Tech SaaS Inc'],
            ['skills' => ['Laravel', 'Vue.js', 'Inertia'], 'position' => 'Full Stack Engineer', 'company' => 'Startup Alpha'],
            ['skills' => ['React', 'TypeScript', 'Next.js'], 'position' => 'Frontend Engineer', 'company' => 'Commerce Beta'],
            ['skills' => ['Vue.js', 'Nuxt', 'Pinia'], 'position' => 'Frontend Engineer', 'company' => 'SaaS Gamma'],
            ['skills' => ['Go', 'gRPC', 'Kubernetes'], 'position' => 'Backend Engineer', 'company' => 'Infra Delta'],
            ['skills' => ['PHP', 'Symfony', 'PostgreSQL'], 'position' => 'Backend Engineer', 'company' => 'Enterprise Epsilon'],
            ['skills' => ['Laravel', 'AWS', 'Docker'], 'position' => 'Cloud Engineer', 'company' => 'Cloud Zeta'],
            ['skills' => ['HR Tech', 'People Analytics', 'SQL'], 'position' => 'HRBP Tech', 'company' => 'People Ops'],
            ['skills' => ['SaaS', 'B2B', 'Sales Engineering'], 'position' => 'Solutions Engineer', 'company' => 'B2B SaaS'],
            ['skills' => ['PdM', 'Discovery', 'Figma'], 'position' => 'Product Manager', 'company' => 'Product Co'],
            ['skills' => ['React', 'GraphQL'], 'position' => 'Frontend Engineer', 'company' => 'Mobile First'],
            ['skills' => ['Laravel', 'Livewire', 'Alpine.js'], 'position' => 'Web Engineer', 'company' => 'Agency Theta'],
            ['skills' => ['Go', 'PostgreSQL'], 'position' => 'Backend Engineer', 'company' => 'Fintech Iota'],
            ['skills' => ['Vue.js', 'Laravel', 'Tailwind'], 'position' => 'Full Stack Engineer', 'company' => 'Agency Kappa'],
            ['skills' => ['スタートアップ', '0→1', 'Laravel'], 'position' => 'Founding Engineer', 'company' => 'Lambda Labs'],
            ['skills' => ['PHP', 'Laravel', 'DDD'], 'position' => 'Backend Engineer', 'company' => 'Domain Driven LLC'],
            ['skills' => ['React', 'Node.js'], 'position' => 'Software Engineer', 'company' => 'Mu Corp'],
            ['skills' => ['HR Tech', 'Laravel', 'Vue.js'], 'position' => 'Engineering Manager', 'company' => 'ATS Vendor Nu'],
        ];

        $candidates = collect($profiles)->map(function (array $p, int $i) {
            return Candidate::query()->create([
                'name' => fake()->name(),
                'email' => "candidate{$i}@example.test",
                'current_company' => $p['company'],
                'current_position' => $p['position'],
                'skills' => $p['skills'],
                'source' => fake()->randomElement(['referral', 'linkedin', 'event']),
                'status' => fake()->randomElement(Candidate::STATUSES),
            ]);
        });

        $stages = Application::STAGES;
        $pairs = [];
        foreach ($candidates as $c) {
            $n = fake()->numberBetween(1, min(3, $jobs->count()));
            foreach ($jobs->shuffle()->take($n) as $j) {
                $pairs[] = [$c->id, $j->id];
            }
        }
        $pairs = collect($pairs)->unique(fn ($p) => $p[0].'-'.$p[1])->take(28)->values();

        foreach ($pairs as [$cid, $jid]) {
            Application::query()->create([
                'candidate_id' => $cid,
                'job_id' => $jid,
                'stage' => fake()->randomElement($stages),
                'score' => fake()->optional(0.7)->numberBetween(1, 5),
                'memo' => fake()->optional(0.4)->sentence(),
            ]);
        }

        // Ensure several Laravel-tagged candidates are in screening for demo prompts
        Candidate::query()->where('skills', 'like', '%Laravel%')->take(4)->update(['status' => 'screening']);
    }
}
