import { Module } from '@nestjs/common';
import { TokensService } from './tokens.service';
import { TokensApiController } from './tokens-api.controller';
import { PrismaModule } from 'nestjs-prisma';
import { TokensViewsController } from '@/tokens/tokens-views.controller';

@Module({
  imports: [PrismaModule],
  controllers: [TokensApiController, TokensViewsController],
  providers: [TokensService],
})
export class TokensModule {}
