import { Module } from '@nestjs/common';
import { TokensService } from './tokens.service';
import { TokensController } from './tokens.controller';
import { PrismaModule } from 'nestjs-prisma';
import { TokensMvcController } from '@/tokens/tokens-views.controller';

@Module({
	imports: [PrismaModule],
	controllers: [TokensController, TokensMvcController],
	providers: [TokensService],
	exports: [TokensService],
})
export class TokensModule {}
