import * as qrcode from 'qrcode';
import { Get, Controller, Render, Param, Res } from '@nestjs/common';
import {
  ApiNotFoundResponse,
  ApiOkResponse,
  ApiOperation,
  ApiParam,
  ApiTags,
} from '@nestjs/swagger';
import { Spot, User } from '@prisma/client';
import { Response } from 'express';
import { ConfigService } from '@nestjs/config';
import { SpotsService } from '@/spots/spots.service';
import { JwtAuth } from '@/auth/jwt/jwt-auth.decorator';
import { FQDN } from '@/config/config.constants';
import { AuthUser } from '@/auth/decorators/auth-user.decorator';
import { ReadSpotDto } from '@/spots/dtos/read-spot.dto';
import { JwtOrTokenOrUnconfiguredSpotAuth } from '@/auth/jwt-or-token-unconfigured-spot/jwt-or-token-or-unconfigured-spot-auth.decorators';

@ApiTags('Views')
@Controller('spots')
export class SpotsViewsController {
  constructor(
    private readonly spotsService: SpotsService,
    readonly configService: ConfigService,
  ) {}

  @Get()
  @JwtAuth()
  @ApiOperation({
    summary: 'Render the spots page',
    description: 'Render the spots page.',
    operationId: 'getSpotsView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/index')
  async getSpotsView(@AuthUser() user: User) {
    const spots = await this.spotsService.getSpots(user);

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return {
      title: 'Spots - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
      spots: spotsDto,
    };
  }

  @Get('create')
  @ApiOperation({
    summary: 'Render the create a new spot page',
    description: 'Render the create a new spot page.',
    operationId: 'createSpotView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/form')
  createSpotView(@AuthUser() user: User) {
    return {
      title: 'Create a new spot - Spot in',
      username: user?.username,
      email: user?.email,
      role: user?.role,
      action: 'POST',
    };
  }

  @Get('latest')
  @ApiOperation({
    summary: 'Render the list of public spots page',
    description: 'Render the list of public spots page.',
    operationId: 'publicSpotsView',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/latest')
  async publicSpotsView() {
    const spots = await this.spotsService.getPublicSpots();

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return {
      title: 'Latest spots - Spot in',
      spots: spotsDto,
    };
  }

  @Get(':id/delete')
  @JwtAuth()
  @ApiOperation({
    summary: 'Delete the specified spot and render the list of spots',
    description: 'Delete the specified spot and render the list of spots.',
    operationId: 'deleteSpotView',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/index')
  async deleteSpotView(@AuthUser() user: User, @Param('id') id: string) {
    await this.spotsService.deleteSpot(id, user);

    const spots = await this.spotsService.getSpots(user);

    const spotsDto = spots.map((spot) => new ReadSpotDto(spot));

    return {
      username: user?.username,
      email: user?.email,
      role: user?.role,
      spots: spotsDto,
    };
  }

  @Get(':id/edit')
  @ApiOperation({
    summary: 'Render the edit a spot page',
    description: 'Render the edit a spot page.',
    operationId: 'editSpotView',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @Render('spots/form')
  @JwtOrTokenOrUnconfiguredSpotAuth()
  async editSpotView(@AuthUser() user: User, @Param('id') id: string) {
    const spot = await this.spotsService.getSpot(id);

    const spotDto = new ReadSpotDto(spot);

    return {
      username: user?.username,
      email: user?.email,
      role: user?.role,
      id,
      values: spotDto,
      action: 'PATCH',
    };
  }

  @Get(':id/redirect')
  @ApiOperation({
    summary: 'Redirect to the link specified by the spot',
    description: 'Redirect to the link specified by the spot.',
    operationId: 'getSpotRedirection',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Redirection success.',
    type: ReadSpotDto,
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found.',
  })
  async getSpotRedirection(
    @Res({ passthrough: true }) res: Response,
    @Param('id') id: string,
  ) {
    const spot = (await this.spotsService.getSpot(id)) as Spot;

    if (!spot.configured) {
      res.redirect(`/spots/${id}/edit`);
    }

    if (spot?.redirection) {
      res.redirect(spot.redirection);
    }
  }

  @Get(':id')
  @ApiOperation({
    summary: 'Render the specified spot page',
    description: 'Render the specified spot page.',
    operationId: 'getSpotView',
  })
  @ApiParam({
    name: 'id',
    description: 'The spot ID.',
    format: 'uuid',
  })
  @ApiOkResponse({
    description: 'Render successful.',
  })
  @ApiNotFoundResponse({
    description: 'Spot has not been found. Redirect to `/not-found` page.',
  })
  async getSpotView(
    @AuthUser() user: User,
    @Res() res: Response,
    @Param('id') id: string,
  ) {
    let spot: Spot;

    try {
      spot = await this.spotsService.getSpot(id);
    } catch (error) {
      return res.redirect('/not-found');
    }

    const fqdn = this.configService.get(FQDN, { infer: true });
    const redirection = `${fqdn}/spots/${spot.id}/redirect`;
    const spotDto = new ReadSpotDto(spot);

    try {
      const qrcodeSvg = await qrcode.toString(redirection, { type: 'svg' });

      return res.render('spots/[id]', {
        username: user?.username,
        email: user?.email,
        role: user?.role,
        title: 'Spot',
        spot: spotDto,
        qrcode: qrcodeSvg,
      });
    } catch (error) {
      console.error(error);
    }
  }
}
