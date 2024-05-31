import { Module } from '@nestjs/common';
import { TokensService } from './tokens.service';
import { TokensApiController } from './tokens-api.controller';
import { PrismaModule } from 'nestjs-prisma';
import { TokensMvcController } from '@/tokens/tokens-mvc.controller';

@Module({
	imports: [PrismaModule],
	controllers: [TokensApiController, TokensMvcController],
	providers: [TokensService],
	exports: [TokensService],
})
export class TokensModule {}
